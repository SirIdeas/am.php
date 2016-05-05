/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Sir Ideas, C. A.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 **/

(function($){

  var instances = [];

  function getInstante(tElement){
    for(var i = 0; i<instances.length; i++){
      if(tElement == instances[i].tElement){
        return instances[i].oOptions;
      }
    }
    return null;
  }

  var oKeys = {
    table : {
      iLen: 'len:int',
      sInputSearchSelector: 'input-search-selector:string',
      sSelectLenSelector: 'select-len-selector',
      sPaginationSelector: 'pagination-selector',
      sCountRecordSelector : 'count-record',
      sRowBeforeFn: 'row-before-fn',
      sRowUiFn: 'row-ui-fn',
      sFieldRowStateClass: 'field-row-state-class',
      oData: {
        sMethod: 'data-method:string',
        sUrl: 'data-url:string'
      }
    },
    attrs:{
      bSort: 'sort:bool',
      bShow: 'show:bool',
      asClass: 'class:array:string'
    },
    pagination: {
      iPaginationItems: 'pagination-items:int'
    }
  };

  var oDefaults = {
    iLen: 10,
    iPaginationItems: 3,
    sInputSearchSelector: null,
    sSelectLenSelector: null,
    sPaginationSelector: null,
    sCountRecordSelector: null,
    sRowBeforeFn: null,
    sRowUiFn: null,
    sFieldRowStateClass: null,
    oColsAttrsDefault : {
      bShow: true,
      bSort: true,
      asClass: []
    },
    fnRecord: function(oData, iRowNumber, oRow){
      return oData;
    },
    oData: {
      aoData: [],
      sUrl: null,
      sMethod: 'get',
      fnParams: function(){ return {} }
    }
  };

  var oExtras = {
    fnAddClases: function(tElement, classes){
      for(var i=0; i<classes.length; i++){
        $(tElement).addClass(classes[i]);
      }
    }
  };

  function init(table, oOptions){

    oOptions = $.extend(true, oOptions, getExtraParams(table, oKeys.table));
    oOptions = $.extend(true, oOptions, getExtraParams(oOptions.sPaginationSelector, oKeys.pagination));

    oOptions.sSearch = '';
    oOptions.iPage = 0;
    oOptions.iPageCount = 1;
    oOptions.aoCols = [];
    oOptions.aoRows = [];
    oOptions.oSorts = [];
    oOptions.oSortOrder = [];
    oOptions.oSearch = {};
    oOptions.oData.iRecordCount = 0;
    oOptions.oData.iRecordFilteredCount = 0;

    oOptions.tElement = table;
    oOptions.jWrap = $('<div class=dinamic-table-wrap/>');
    oOptions.jMaskLoading = $('<div class=dinamic-table-mask-loading/>');
    oOptions.tMsgShowing = $(oOptions.sCountRecordSelector).find('[dinamic-table-msg="showing"]');
    oOptions.sMsgShowing = oOptions.tMsgShowing.html();
    oOptions.tMsgNoRecordsFiltered = $(oOptions.sCountRecordSelector).find('[dinamic-table-msg="no-records-filtered"]');
    oOptions.sMsgNoRecordsFiltered = oOptions.tMsgNoRecordsFiltered.html();
    oOptions.tMsgNoRecords = $(oOptions.sCountRecordSelector).find('[dinamic-table-msg="no-records"]');

    $(oOptions.sInputSearchSelector).keypress(function(){
      setSearch(oOptions, $(this).val());
    });

    $(oOptions.sSelectLenSelector).change(function(){
      setLen(oOptions, $(this).val());
    });

    if($(oOptions.sSelectLenSelector).length>0){
      oOptions.iLen = parseInt($(oOptions.sSelectLenSelector).val());
    }

    $(oOptions.sPaginationSelector).find('[data-param-page]').click(function(){
      setPage(oOptions, $(this).attr('data-param-page'));
    });

    $(table).find('thead th').each(function(i, th){

      var attrs = $.extend({}, oOptions.oColsAttrsDefault);
      var sortAsc = $(th).find('[data-param-sort-asc]').hide();
      var sortDesc = $(th).find('[data-param-sort-desc]').hide();
      var sortContainer = $('<a href="#"/>')
        .click(function(){
          var dir = undefined;
          if(sortAsc.is(':visible')){
            sortAsc.hide();
            sortDesc.show();
            dir = 'desc';
          }else if(!sortDesc.is(':visible')){
            sortAsc.show();
            dir = 'asc';
          }else{
            sortDesc.hide();
            sortAsc.hide();
          }
          addSort(oOptions, i, dir);
        });

      oOptions.aoCols[i] = {
        iPos: i,
        tElement: th,
        oAttrs: attrs,
        tSortContainer: sortContainer
      };

      setColAttrs(oOptions, i, getExtraParams(th, oKeys.attrs));

    });

    refreshAll(oOptions);

    $(table)
      .wrap(oOptions.jWrap)
      .addClass('dinamic-table');

    $(table).parent().append(oOptions.jMaskLoading);

    return {
      tElement: table,
      oOptions : oOptions
    };

  }

  function getExtraParams(tElement, keys){
    var parts, value, ret = {}, count=0;
    for(var i in keys){
      if(typeof(keys[i]) == 'string'){
        parts = keys[i].split(':');
        value = $(tElement).attr('data-param-' + parts[0]);
        if(value != undefined){
          switch(parts[1]){
            case 'string':
              break;
            case 'int':
              value = parseInt(value);
              break;
            case 'bool':
              value = value=='false' ? false : value=='true' ? true : null;
              break;
            case 'array':
              value = value.split(',');
              break;
          }
          ret[i] = value
          count++;
        }
      }else{
        ret[i] = getExtraParams(tElement, keys[i]);
        if(!ret[i]) ret[i] = {};
      }
    }
    return count==0? null : ret;
  }

  function getBody(oOptions){
    var tbody = $(oOptions.tElement).find('tbody');
    if(tbody.length==0){
      tbody = $('<tbody/>');
      $(oOptions.tElement).append(tbody);
    }
    return tbody;
  }

  function setColAttrs(oOptions, pos, attrs){
    var aCol = oOptions.aoCols[pos];
    aCol.oAttrs = $.extend(aCol.oAttrs, attrs);
    updateColsAttrs(oOptions, pos);
  }

  function updateColsAttrs(oOptions, pos){

    var col = oOptions.aoCols[pos];

    if(col.oAttrs.bSort){
      $(col.tSortContainer).append($(col.tElement).children());
      $(col.tElement).append($(col.tSortContainer));
    }else if($(col.tElement).parent() == $(col.tSortContainer)){
      $(col.tSortContainer).parent().append($(col.tElement));
      $(col.tSortContainer).remove();
    }
    if(col.oAttrs.bShow) $(col.tElement).show(); else $(col.tElement).hide();
    oExtras.fnAddClases(col.tElement, col.oAttrs.asClass);

    updateCol(oOptions, pos);

  }

  function updateCols(oOptions){
    for(var i=0; i<oOptions.aoCols.length; i++){
      updateCol(oOptions, i);
    }
  }

  function updateCol(oOptions, pos){
    var td, attr = oOptions.aoCols[pos].oAttrs;
    for(var i=0; i<oOptions.aoRows.length; i++){
      td = oOptions.aoRows[i].aoCells[pos];
      if(attr.bShow) $(td.tElement).show(); else $(td.tElement).hide();
      oExtras.fnAddClases(td.tElement, attr.asClass);
    }
  }

  function addSort(oOptions, pos, dir){
    var oldOrder = oOptions.oSortOrder[pos];
    oOptions.oSortOrder[pos] = undefined;
    if(oldOrder != undefined){
      oOptions.oSorts[oldOrder] = undefined;
    }
    if(dir!=undefined){
      oOptions.oSortOrder[pos] = oOptions.oSorts.length;
      oOptions.oSorts.push({
        pos: pos,
        dir: dir
      });
    }
    refreshAll(oOptions);
  }

  function setData(oOptions, params){
    oOptions.oData = $.extend(oOptions.oData, params);
    refreshAll(oOptions);
  }

  function setRecords(oOptions, records){
    var oData = oOptions.oData;
    oData.aoData = records;
    oData.iRecordCount = records.length;
    oData.iRecordFilteredCount = records.length;
    refreshData(oOptions);
  }

  function isValidServerParams(oOptions){
    var oData = oOptions.oData;
    return oData.sUrl != null && (oData.sMethod == 'get' || oData.sMethod == 'post')
  }

  function refreshAll(oOptions){
    if(isValidServerParams(oOptions)){
      loadData(oOptions);
    }else{
      refreshData(oOptions);
    }
  }

  function addRecord(oOptions, record){
    var oData = oOptions.oData;
    oData.aoData.push(record);
    oData.iRecordCount++;
    oData.iRecordFilteredCount++;
    refreshData(oOptions);
  }

  function removeRecord(oOptions, pos){
    var oData = oOptions.oData;
    oData.aoData[pos] = undefined;
    oData.aoData = oData.aoData.filter(function(n){return n});
    oData.iRecordCount--;
    oData.iRecordFilteredCount--;
    refreshData(oOptions);
  }

  function loadData(oOptions){

    var oData = oOptions.oData;
    var params = oOptions.oData.fnParams();

    params['iLen'] = oOptions.iLen;
    params['iPage'] = oOptions.iPage;
    params['sSearch'] = oOptions.sSearch;
    params['oSearch'] = oOptions.oSearch;
    params['oSorts'] = oOptions.oSorts.filter(function(n){return n});

    oOptions.jMaskLoading.show();

    $[oData.sMethod](oData.sUrl, params, function(response){
      oData.aoData = response.aoData;
      oData.iRecordCount = parseInt(response.iRecordCount);
      oData.iRecordFilteredCount = parseInt(response.iRecordFilteredCount);
      refreshData(oOptions);
    });

  }

  function refreshData(oOptions){
    var oData = oOptions.oData;
    var mod, pages = 1;
    if(oOptions.iLen!=-1){
      pages = oData.iRecordFilteredCount;
      mod = pages%oOptions.iLen;
      pages = Math.floor(pages/oOptions.iLen) + (mod==0? 0 : 1);
    }
    oOptions.iPageCount = parseInt(pages);
    if(oOptions.iPage>=oOptions.iPageCount && oOptions.iPageCount!=0){
      oOptions.iPage = Math.max(0, oOptions.iPageCount-1);
      refreshAll(oOptions);
    }else{
      updateLen(oOptions);
      oOptions.jMaskLoading.hide();
    }
  }

  function updateRecordCount(oOptions){

    var oData = oOptions.oData;

    $(oOptions.sCountRecordSelector).find('[dinamic-table-msg]').hide();

    var co = oData.iRecordCount;
    var fc = oData.iRecordFilteredCount;
    var rf = (oOptions.iLen==-1? 0 : oOptions.iLen) * oOptions.iPage+1;
    var rt = rf+oOptions.iLen-1;

    if(rt<0){
      rt = fc;
    }else{
      rt = Math.min(rt, fc);
    }

    if(co == 0){
      oOptions.tMsgNoRecords.show();
    }else if(fc == 0){
      if(oOptions.sMsgNoRecordsFiltered){
        oOptions.tMsgNoRecordsFiltered.html(
          oOptions.sMsgNoRecordsFiltered
            .split('{$co}').join(co))
          .show();
      }
    }else{
      if(oOptions.sMsgShowing){
        oOptions.tMsgShowing.html(
          oOptions.sMsgShowing
            .split('{$rf}').join(rf)
            .split('{$rt}').join(rt)
            .split('{$fc}').join(fc)
            .split('{$co}').join(co))
          .show();
      }
    }

  }

  function updateData(oOptions){

    var aoData = oOptions.oData.aoData;
    var aoRows = oOptions.aoRows;
    var len = aoData.length;
    for(var i=0; i<aoRows.length; i++){
      (function(oRow, oData){
        if(i<len){
          callRowBeforeFn(oOptions.sRowBeforeFn, oData)
          updateRow(oRow.aoCells, oOptions.fnRecord(oData, i, oRow.tElement));
          callRowUiFn(oOptions.sRowUiFn, oData, oRow.tElement);
          updateRowClass(oRow, oOptions.sFieldRowStateClass, oData);
          $(oRow.tElement).show();
        }else{
          $(oRow.tElement).hide();
        }
      })(aoRows[i], aoData[i]);

    }

  }

  function setLen(oOptions, len){
    if(oOptions.iLen != len){
      oOptions.iLen = parseInt(len);
      refreshAll(oOptions);
    }
  }

  function updateLen(oOptions){
    var aoData = oOptions.oData.aoData;
    var tr, tBody = getBody(oOptions);
    var len = Math.max(aoData.length, oOptions.aoRows.length);
    for(var i=0; i<len; i++){
      if(oOptions.aoRows[i] == undefined && (i<aoData.length)){
        tr = newRow(oOptions);
        tBody.append(tr.tElement);
        oOptions.aoRows.push(tr);
      }
    }
    updatePagination(oOptions);
    updateData(oOptions);
    updateCols(oOptions);
    updateRecordCount(oOptions);
  }

  function newRow(oOptions){
    var td, sClass = '', ret = {
      tElement: $('<tr/>'),
      aoCells: [],
      setClass: function(sNewClass){
        ret.tElement
          .removeClass(sClass)
          .addClass(sClass = sNewClass);
      }
    };
    for(var i=0; i<oOptions.aoCols.length; i++){
      td = $('<td/>');
      ret.tElement.append(td);
      ret.aoCells.push({
        tElement: td
      });
    }
    return ret;
  }

  function updateRow(aoCells, data){
    for(var i=0; i<aoCells.length; i++){
        $(aoCells[i].tElement).empty().append(data[i]);
    }
  }

  function callRowUiFn(sRowUiFn, oData, tElement){
    if(sRowUiFn!==null){
      eval(sRowUiFn + '(oData, tElement)');
    }
  }

  function callRowBeforeFn(sRowBeforeFn, oData){
    if(sRowBeforeFn!==null){
      eval(sRowBeforeFn + '(oData)');
    }
  }

  function updateRowClass(oRow, sFieldRowStateClass, oData){
    if(sFieldRowStateClass){
      oRow.setClass(oData[sFieldRowStateClass]);
    }
  }

  function setSearch(oOptions, str){
    oOptions.sSearch = str;
    refreshAll(oOptions);
  }

  function setPage(oOptions, page){
    switch(page){
      case 'first': page = 0; break;
      case 'prev': page = Math.max(oOptions.iPage-1, 0); break;
      case 'next': page = Math.min(oOptions.iPage+1, oOptions.iPageCount-1); break;
      case 'last': page = oOptions.iPageCount-1; break;
      default: page = parseInt(page); break;
    }
    if(oOptions.iPage != page){
      oOptions.iPage = page;
      refreshAll(oOptions);
    }
  }

  function setPageCount(oOptions, pageCount){
    if(oOptions.iPageCount != pageCount){
      oOptions.iPageCount = parseInt(pageCount);
      setPageCount(oOptions, oOptions.iPageCount-1);
      updatePagination(oOptions);
    }
  }

  function updatePagination(oOptions){
    var page = oOptions.iPage;
    var pageCount = oOptions.iPageCount;
    var pageItems = oOptions.iPaginationItems;

    var from = page - pageItems;
    var to = page + pageItems;

    if(from<0){
      to -= from;
      from = 0;
    }

    if(to>(pageCount-1)){
      from -= (to-(pageCount-1));
      if(from<0){
        from = 0;
      }
      to = pageCount-1;
    }

    $(oOptions.sPaginationSelector).find('[data-param-page="first"]').parent()[page == 0? 'addClass' : 'removeClass']('disabled');
    $(oOptions.sPaginationSelector).find('[data-param-page="prev"]').parent()[page == 0? 'addClass' : 'removeClass']('disabled');

    $(oOptions.sPaginationSelector).find('[data-param-page="next"]').parent()[page == pageCount-1 || pageCount == 0? 'addClass' : 'removeClass']('disabled');
    $(oOptions.sPaginationSelector).find('[data-param-page="last"]').parent()[page == pageCount-1 || pageCount == 0? 'addClass' : 'removeClass']('disabled');

    var len = Math.max(to-from+1, $(oOptions.sPaginationSelector).find('li.number').length);

    for(var i=from, j=0; j<len; i++, j++){
      if(i<=to){
        if($(oOptions.sPaginationSelector).find('li.number a').eq(j).length == 0){
          $('<li class="number"><a href="#" data-param-page="'+j+'">'+(j+1)+'</a></li>').click(function(){
            setPage(oOptions, $(this).find('a').attr('data-param-page'));
          }).insertBefore($(oOptions.sPaginationSelector).find('[data-param-page="next"]').parent());
        }
        $(oOptions.sPaginationSelector).find('li.number').eq(j)
          .show().find('a').attr('data-param-page', i).html(i+1);
      }else{
        $(oOptions.sPaginationSelector).find('li.number').eq(j).hide();
      }
    }

    $(oOptions.sPaginationSelector).find('li.number.active a').parent().removeClass('active');
    $(oOptions.sPaginationSelector).find('li.number a[data-param-page="'+page+'"]').parent().addClass('active');

  }

  $.fn.extend({
    dinamictable: function(pOptions, p1, p2){
      return this.each(function(){
  //      if (this.type != 'table') {
  //        return false;
  //      }
        var oOptions = getInstante(this);
        if(oOptions == null){
          oOptions = $.extend({}, oDefaults, typeof(oOptions) == 'string'? {} : pOptions);
          instances.push(init(this, oOptions))
        }
        if(typeof(pOptions) == 'string'){
          switch(pOptions){
            case 'setData': setData(oOptions, p1); break;
            case 'setRecords': setRecords(oOptions, p1); break;
            case 'setLen': setLen(oOptions, p1); break;
            case 'setColAttrs': setColAttrs(oOptions, p1, p2); break;
            case 'setSearch': setSearch(oOptions, p1); break;
            case 'setPage': setPage(oOptions, p1); break;
            case 'addRecord': addRecord(oOptions, p1); break;
            case 'removeRecord': removeRecord(oOptions, p1); break;
          }
        }
      });
    }
  });

})($);
