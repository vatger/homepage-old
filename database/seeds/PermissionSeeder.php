<?php

use Illuminate\Database\Seeder;
use Junges\ACL\Exceptions\PermissionAlreadyExistsException;

class PermissionSeeder extends Seeder
{

	protected $_permissions = [
		['Administrationszugang', 'administration', 'Gewährt Zugang zu der Administration.'],
		['Mitglieder / Gruppenverwaltung', 'administration.membership', 'Erlaubt die Administration von Gruppen und Mitgliedern.'],
		['Mitglieder / Impersonation', 'administration.impersonate', 'Erlaubt es, in die Rolle eines anderen Nutzers zu schlüpfen.'],
		['Navigationsverwaltung', 'administration.navigation', 'Erlaubt die Verwaltung von Flugplätzen und ATS Stationen.'],
		['Navigationsverwaltung RG', 'administration.navigation.rg', 'Erlaubt die Verwaltung von RG bezogenen Flugplätzen und ATS Stationen.'],
		['Regionalgruppenverwaltung', 'administration.regionalgroup', 'Erlaubt die Verwaltung von Regionalgruppen.'],
		['Regionalgruppenverwaltung RG', 'administration.regionalgroup.rg', 'Erlaubt die Verwaltung der eigenen Regionalgruppe.'],
		['ATD Verwaltung', 'administration.atd', 'Erlaubt die Verwaltung des ATD. (Recht für die ATD Leitung)'],
		['Forum / Forengruppenverwaltung', 'administration.forumgroups', 'Erlaubt das Anlegen und Entfernen von Forengruppen.'],
		['Bildverwaltung / Manage', 'administration.images.manage', 'Erlaubt es Bilder, die auf den vACC Germany Webserver geladen wurden, freizugeben und zu löschen.'],
		['Bildverwaltung / Upload', 'administration.images.upload', 'Erlaubt das Hinterlegen von Bildern auf dem vACC Germany Webserver.'],
	];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->getOutput()->writeln('Starting PermissionSeeder.');
        
        $this->command->getOutput()->writeln('Removing old permissions.');
        $permissionModel = app(config('acl.models.permission'));
        $permissionModel->delete();

        $this->command->getOutput()->writeln('Starting seeding of new information...');
        $this->command->getOutput()->progressStart(count($this->_permissions));

        foreach ($this->_permissions as $permission) {
            try {
        		$permissionModel = app(config('acl.models.permission'));
	            try {
	                $p = $permissionModel->where('slug', $permission[1])
	                    ->orWhere('name', $permission[0])
	                    ->first();
	                if (! is_null($p)) {
	                    throw PermissionAlreadyExistsException::create();
	                }
	                $permissionModel->create([
	                    'name'        => $permission[0],
	                    'slug'        => $permission[1],
	                    'description' => $permission[2],
	                ]);
	            } catch (\Exception $exception) {
	                $this->command->getOutput()->writeln($exception->getMessage());
	            }
	        } catch (\Exception $exception) {
	            $this->command->getOutput()->writeln($exception->getMessage());
	        }
	        $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->getOutput()->writeln('Finished seeding.');
    }
}
