;(function($) {
  $(document).ready(function() {
    $('#tele').njtTelegram({
      accounts: njt_tele.accounts,
      timezone: njt_tele_global.timezone,
      gdprStatus: njt_tele.gdprStatus,
      defaultAvatar: njt_tele_global.defaultAvatarSVG,
      options: njt_tele.options,
    })
  })
})(jQuery)
