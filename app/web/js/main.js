function newNotification() {
  if (Notification.permission !== "granted") {
    Notification.requestPermission();
  }

  return new Notification('Lemur', {
    icon: '/images/favicon-160x160.png',
    body: "There is a new pull request",
  });
}

var refresh = function() {
  $.get("/refresh", function(data) {
    $("title").html('(' + data.number_pull_requests + ') Lemur');
    $("#pull-requests-wrapper").html(data.html);
    jQuery("time.timeago").timeago();
    if (data.number_pull_requests > localStorage.getItem('numberPullRequests')) {
      newNotification();
    }
    localStorage.setItem('numberPullRequests', data.number_pull_requests);
  })
};

refresh();
setInterval(refresh, 10000);
