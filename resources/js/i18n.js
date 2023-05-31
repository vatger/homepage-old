window.selectedLanguage = document.head.querySelector('meta[name="lang"]').content;

window.trans = function(key, replace = {}) {
    let translation = key.split('.').reduce((t,i) => t[i] || null, window.i18n);
    for(var placeholder in replace) {
        translation = translation.replace(`:${placeholder}`, replace[placeholder]);
    }
    return translation;
};

window.trans_choice = function(key, i, replace = {}) {
    let translation = key.split('.').reduce((t,i) => t[i] || null, window.i18n).split('|');
    if (typeof(translation) == 'undefined') return "";
    var regEx = /(\[\d+\s*,\s*(\d+|\*)\])|({\d+})/;
    var numSplit = /(\d+)|(\*)/;
    var splitreg = /((\])|(}))\s(.*)/;
    for (var j = translation.length - 1; j >= 0; j--) {
        var t = translation[j].match(regEx)[0].match(numSplit);
        var l = t[0];
        var h = t[1];
        if(typeof(h) == 'undefined' || h == '*') h = 999999999;
        if(l <= i && i <= h){
            var tr = translation[j].split(splitreg)[4];
            tr.replace(':count', i);
            for (var placeholder in replace) {
                tr = tr.replace(`:${placeholder}`, replace[placeholder]);
            }
            return tr;
        }
    }
    return translation;
};