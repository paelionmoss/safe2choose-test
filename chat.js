<script type="text/javascript">
  var url = window.location.href;
  var locale = url.split("/")[3];
  var onlineGreeting, offlineGreeting;
  
  onlineGreeting = "";
  offlineGreeting = "";
  
  switch (locale) {
    case "es":
      onlineGreeting = "Habla con nosotras";
      offlineGreeting = "Deja un mensaje";
      break;
    case "ja":
      onlineGreeting = "私たちとチャット";
      offlineGreeting = "私達にメッセージを残します";
      break;
    case "it-it":
      onlineGreeting = "Chatta con noi";
      offlineGreeting = "Ci lascia un messaggio";
      break;
    // NOTE: It seems a bit weird that these languages don't have translated greetings but 
    //       this is just refactoring so I'm leaving behavior as is.
    case "pl":
    case "pt-pt":
    case "hi":
    case "fr":
    default:
      onlineGreeting = "Chat with us";
      offlineGreeting = "Leave us a message";
  }
  
  window.$zopim||(function(d,s){var z=$zopim=function(c){
    z._.push(c)},$=z.s=
    d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
    _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
    $.src='//v2.zopim.com/?';z.t=+new Date;$.
    type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
  
  $zopim.livechat.setGreetings({
	    'online': onlineGreeting,
	    'offline': offlineGreeting
	    });
  
  </script>