<?php

namespace App\Libraries;


use App\Models\Membership\Account as Account;
use App\Models\Navigation\Chart;
use App\Models\Regionalgroups\Regionalgroup;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class Gitlab
{
    private Client $client;
    public function __construct()
    {
        $uri = null;//config('gitlab.url'); //https://gitlab.example.com/api/v4
        $access_token = null; //config('gitlab.apikey');

        $this->client = new Client([
            'base_uri' => $uri,
            'connect_timeout' => 30,
            'read_timeout' => 30,
            'timeout'=> 30,
            'headers' => [
                'Authorization' => "Bearer {$access_token}",
            ],
        ]);
    }


    public function createAccount(Account $account) : bool
    {
        return false;
        if($account->setting->gitlab_id != null) return false; //already has gitlab account
        $res = $this->client->request('POST', 'users', ['form_params' =>
            [
                'username' => $account->id,
                'name' => $account->username,
                'email' => $account->email,
                'reset_password' => true,
            ],
        ]);
        if ($res->getStatusCode()< 200 || $res->getStatusCode() > 299) return false;
        $json = json_decode($res->getBody()->getContents());
        /*$createUserResponse = '{"id":75,"username":"10000001","name":"Web One","state":"active","avatar_url":"https://secure.gravatar.com/avatar/5bf979420dd45b7c9b1ad403fe051cae?s=80\u0026d=identicon","web_url":"https://git.vatsim-germany.org/10000001","created_at":"2022-03-16T12:55:35.202Z","bio":"","location":null,"public_email":null,"skype":"","linkedin":"","twitter":"","website_url":"","organization":null,"job_title":"","pronouns":null,"bot":false,"work_information":null,"followers":0,"following":0,"local_time":null,"last_sign_in_at":null,"confirmed_at":null,"last_activity_on":null,"email":"auth.dev1@vatsim.net","theme_id":1,"color_scheme_id":1,"projects_limit":100,"current_sign_in_at":null,"identities":[],"can_create_group":true,"can_create_project":true,"two_factor_enabled":false,"external":false,"private_profile":false,"commit_email":"auth.dev1@vatsim.net","is_admin":false,"note":null}';*/
        $account->setting->gitlab_id = $json->id;
        $account->setting->save();
        return true;
    }

    public function checkNAVAssignments(Account $account) : void
    {
        return;
        if($account->setting->gitlab_id == null) return; //user has no account
        //now this will not be good, but it will work until V3 :)
        $rgs = Regionalgroup::all();
        $mapping = [
            2 => 119, //edbb
            1 => 116, //edww
            3 => 115, //edll
            4 => 114, //edff
            5 => 117, //edmm
            // non-existing rg id to remove members
            102 => 92, //edbb old group
            101 => 93, //edww old group
            103 => 94, //edll old group
            104 => 95, //edff old group
            105 => 96, //edmm old group
        ];
        foreach ($rgs as $rg) {
            $rg_gitlab_group = $mapping[$rg->id];
            if($rg->navigators()->wherePivot('account_id', $account->id)->count()){
                try {
                    $this->assignToGroup($account, $rg_gitlab_group);
                } catch (\Exception $e){}
            } else {
                try {
                    $this->removeFromGroup($account, $rg_gitlab_group);
                } catch (\Exception $e){}
            }
        }
    }

    //$gitlab_access_level: No access (0), Minimal access (5), Guest (10), Reporter (20), Developer (30), Maintainer (40), Owner (50)
    public function assignToGroup(Account $account, int $gitlab_group_id, int $gitlab_access_level = 30) : bool
    {
        return false;
        if($account->setting->gitlab_id == null) return false; //has no gitlab account
        $res = $this->client->request('POST', "groups/{$gitlab_group_id}/members", ['form_params' =>
            [
                'user_id' => $account->setting->gitlab_id,
                'access_level' => $gitlab_access_level,
            ],
        ]);
        if ($res->getStatusCode()< 200 || $res->getStatusCode() > 299) return false;
        return true;
    }

    public function removeFromGroup(Account $account, int $gitlab_group_id) : bool
    {
        return false;
        if($account->setting->gitlab_id == null) return false; //has no gitlab account
        $res = $this->client->request('DELETE', "groups/{$gitlab_group_id}/members/{$account->setting->gitlab_id}");
        if ($res->getStatusCode()< 200 || $res->getStatusCode() > 299) return false;
        return true;
    }

    public function getDocumentInfo(int $repository_id, string $document_path,  string $branch_name="main")
    {
        return false;
        $document_path = urlencode($document_path);
        $branch_name = urlencode($branch_name);
        $body_contents = \Cache::remember("gitlab.fileinfo.repo_{$repository_id}.{$document_path}.branch_{$branch_name}", 60, function () use ($repository_id, $document_path, $branch_name ){
            $res = $this->client->request('GET', "projects/{$repository_id}/repository/files/{$document_path}", ['form_params' =>
                [
                    'ref' => $branch_name,
                ],
            ]);
            if ($res->getStatusCode()< 200 || $res->getStatusCode() > 299) return false;
            return $res->getBody()->getContents();
        });
        if($body_contents == false) abort(404);
        return json_decode($body_contents);
    }

    public function saveDocumentRaw(int $repository_id, string $document_path,  string $branch_name="main")
    {
        return false;
        $document_path = urlencode($document_path);
        $branch_name = urlencode($branch_name);
        $path = \Cache::remember("gitlab.fileurl.repo_{$repository_id}.{$document_path}.branch_{$branch_name}", 60, function () use ($repository_id, $document_path, $branch_name ){
            $res = $this->client->request('GET', "projects/{$repository_id}/repository/files/{$document_path}/raw", ['form_params' =>
                [
                    'ref' => $branch_name,
                ],
            ]);
            $documentInfo = $this->getDocumentInfo($repository_id, $document_path,  $branch_name);
            if ($res->getStatusCode()< 200 || $res->getStatusCode() > 299 || $documentInfo == false) return false;

            $contents = $res->getBody()->getContents();
            $storage_path = "public/gitlab/repo_{$repository_id}/{$documentInfo->file_path}";
            if(!Storage::put($storage_path, $contents)) return false;
            return $storage_path;
        });
        return $path;
    }

    public function generateChartLink(Chart $chart)
    {
        return false;
        if(!$chart->getIsGitlabAttribute()) return false;
        if(!$chart->public_available) return false; //non-public access is not implemented yet
        try {
            $string = $chart->href; //gitlab:edff_public/path/to/file.pdf
            $pos = strpos($string, 'gitlab:'); if($pos===false) return false;
            $string = substr_replace($string, '', $pos, strlen('gitlab:'));
            $pos = strpos($string, '/'); if($pos===false) return false;
            $prefix = substr($string,0,$pos); //edff_public
            $pos = strpos($string, $prefix); if($pos===false) return false;
            $path_to_file = substr_replace($string, '', $pos, strlen($prefix)+1); // path/to/file.pdf
            //this again will be shady: prefix -> gitlab repo
            $mappings=[
                'edbb_public' => 57,
                'edww_public' => 58,
                'edll_public' => 59,
                'edff_public' => 60,
                'edmm_public' => 61,
            ];
            $repo_id = $mappings[$prefix];
            $storage_path = $this->saveDocumentRaw($repo_id, $path_to_file);
            return Storage::url($storage_path);
        } catch (\Exception $e) {}
        return false;
    }





    /*public function ddProjects()
    {
        $res = $this->client->request('GET', 'projects');
        if ($res->getStatusCode()< 200 || $res->getStatusCode() > 299) return false;
        $json = json_decode($res->getBody()->getContents());
        dd($json);
    }*/
}
