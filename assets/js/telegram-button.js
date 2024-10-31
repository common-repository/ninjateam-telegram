;(function($) {
  $(document).ready(function() {
    $('.nta_tele_button').each(function(i, element) {
      const id = $(element).data('id')
      $(element).njtTelegramButton({
        ...window['njt_tele_button_' + id],
        timezone: njt_tele_global.timezone,
        i18n: njt_tele_global.i18n,
      })
    })
  })
})(jQuery)
