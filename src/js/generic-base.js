// Define globals for JSHint validation:
/* global console, PostTypeSearchDataManager */


var Generic = {};


Generic.addBodyClasses = function($) {
  // Assign browser-specific body classes on page load
    var bodyClass = '';
    // Old IE:
    if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) { //test for MSIE x.x;
            var ieversion = Number(RegExp.$1); // capture x.x portion and store as a number

            if (ieversion >= 10)     { bodyClass = 'ie ie10'; }
            else if (ieversion >= 9) { bodyClass = 'ie ie9'; }
            else if (ieversion >= 8) { bodyClass = 'ie ie8'; }
            else if (ieversion >= 7) { bodyClass = 'ie ie7'; }
    }
     // IE11+:
    else if (navigator.appName === 'Netscape' && !!navigator.userAgent.match(/Trident\/7.0/)) { bodyClass = 'ie ie11'; }
    // iOS:
    else if (navigator.userAgent.match(/iPhone/i)) { bodyClass = 'iphone'; }
    else if (navigator.userAgent.match(/iPad/i))   { bodyClass = 'ipad'; }
    else if (navigator.userAgent.match(/iPod/i))   { bodyClass = 'ipod'; }
    // Android:
    else if (navigator.userAgent.match(/Android/i)) { bodyClass = 'android'; }

    $('body').addClass(bodyClass);
};

Generic.handleExternalLinks = function($){
  $('a:not(.ignore-external)').each(function(){
    var url  = $(this).attr('href');
    var host = window.location.host.toLowerCase();

    if (url && url.search(host) < 0 && url.search('http') > -1){
      $(this).attr('target', '_blank');
      $(this).addClass('external');
    }
  });
};


if (typeof jQuery !== 'undefined'){
  (function(){
    $(document).ready(function() {
      Generic.addBodyClasses($);
      Generic.handleExternalLinks($);
    });
  })(jQuery);
}
