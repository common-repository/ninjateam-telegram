jQuery(document).ready(function () {
  jQuery('#njt-tele-ads').click(function () {
    jQuery
      .ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'njt_tele_ads_save',
          nonce: window.njt_admin_ads.nonce,
        },
      })
      .done(function (result) {
        if (result.success) {
          jQuery('#njt-tele-ads-wrapper').hide('slow')
        } else {
          console.log('Error', result.data.status)
        }
      })
  })
})
