$ () ->
  
  $('.spyscroll[data-equal]').bind 'spyscroll', (e, scrollTop) ->
    value = eval($(this).attr('data-equal'))
    cls = $(this).attr('data-class')

    if scrollTop is value
      console.log(this, cls)
      $(this).addClass(cls)
    else
      $(this).removeClass(cls)

  $('.spyscroll[data-not-equal]').bind 'spyscroll', (e, scrollTop) ->
    value = eval($(this).attr('data-not-equal'))
    cls = $(this).attr('data-class')

    if scrollTop isnt value
      console.log(this, cls)
      $(this).addClass(cls)
    else
      $(this).removeClass(cls)

  $('.spyscroll[data-height]').bind 'spyscroll', (e, scrollTop) ->
    target = $($(this).attr('data-height'))
    cls = $(this).attr('data-class')

    if target.length>0
      if scrollTop >= target.outerHeight()
        console.log(this, cls)
        $(this).addClass(cls)
      else
        $(this).removeClass(cls)
    
  # Spy scroll
  $(document).scroll () ->
    $('.spyscroll').trigger 'spyscroll', $(this).scrollTop()


  $(document).trigger 'scroll'







