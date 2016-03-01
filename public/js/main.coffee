$ () ->

  do ->
    $('.spyscroll[data-nav]').each () ->
      cls = $(this).data('class')
      target = $($(this).data('nav'))
      relative = $($(this).data('relative')).offset().top
      current = null
      ret = $(this)
        .find('a')
        .map () ->
          selector = $(this).attr('href').replace(/.*(?=#[^\s]+$)/, '')
          [[$(selector+':visible').offset().top - $(selector).outerHeight() - relative, this]]
        .sort (a, b) ->
          a[0] - b[0]

      $(this).bind 'spyscroll', (e, scrollTop) ->
        $(current).parents('li').removeClass(cls)
        current = null
        for item in ret
          if item[0]>=scrollTop
            break
          current = item[1]

        $(current).parents('li').addClass(cls)

  # Diferentes spy controls
  $('.spyscroll[data-equal]').bind 'spyscroll', (e, scrollTop) ->
    cls = $(this).data('class')
    value = eval($(this).data('equal'))
    method = if scrollTop is value then 'addClass' else 'removeClass'
    $(this)[method](cls)

  $('.spyscroll[data-not-equal]').bind 'spyscroll', (e, scrollTop) ->
    cls = $(this).data('class')
    value = eval($(this).data('not-equal'))
    method = if scrollTop isnt value then 'addClass' else 'removeClass'
    $(this)[method](cls)

  $('.spyscroll[data-height]').bind 'spyscroll', (e, scrollTop) ->
    cls = $(this).data('class')
    target = $($(this).data('height'))
    height = target.outerHeight()

    if target.length>0
      method = if scrollTop >= height then 'addClass' else 'removeClass'
      $(this)[method](cls)

  # Spy scroll
  $(document).scroll () ->
    $('.spyscroll').trigger 'spyscroll', $(this).scrollTop()


  $(document).trigger 'scroll'







