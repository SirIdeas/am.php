$ () ->
  
  # Spy scroll
  $('.spyscroll').each () ->

    self = this
    target = $(this).attr('data-target')
    eq = eval($(this).attr('data-eq'))
    neq = eval($(this).attr('data-neq'))
    strClass = $(this).attr('data-class')

    $(document).scroll () ->
      scrollValue = $(this).scrollTop()

      if $(target).length>0
        if scrollValue >= $(target).outerHeight()
          $(self).addClass(strClass)
        else
          $(self).removeClass(strClass)

      else if eq isnt undefined
        if scrollValue is eq
          $(self).addClass(strClass)
        else
          $(self).removeClass(strClass)

      else if neq isnt undefined
        if scrollValue isnt neq
          $(self).addClass(strClass)
        else
          $(self).removeClass(strClass)


  $(document).trigger 'scroll'







