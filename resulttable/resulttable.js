function baseInit()
{
  if( typeof baseInit.done != 'undefined' ) return;
  baseInit.done = true;
  $.getScript('resulttable/excellentexport.min.js');
  $.getScript('resulttable/jszip.min.js');
  $.getScript('resulttable/jspdf.min.js');

  $('<link/>', {
    rel: 'stylesheet',
    href: 'resulttable/resulttable.css'
  }).appendTo('head');
}


function initResultTable(el) {

  baseInit();
  var tblid = 'maindata';
  var tableEl = $(el).find('table').get(0);
  tableEl.id = tblid;

  analyseTableContent(el, tableEl);
  var box = $('<div class="resultactionbar">');
  //add top level menus
  $('<a>Export</a>').appendTo(box)
    .click(function() {
      $('#exportmenu').toggle();
      $('#zoommenu').toggle(false);
      $('#imagemenu').toggle(false);
      $('#filtermenu').toggle(false);
      $('#sortmenu').toggle(false);
    });
  $('<a>Zoom</a>').appendTo(box)
    .click(function() {
      $('#exportmenu').toggle(false);
      $('#zoommenu').toggle();
      $('#imagemenu').toggle(false);
      $('#filtermenu').toggle(false);
      $('#sortmenu').toggle(false);
    });
  $('<a>Filter</a>').appendTo(box)
    .click(function() {
      $('#filtermenu').toggle();
      $('#exportmenu').toggle(false);
      $('#zoommenu').toggle(false);
      $('#imagemenu').toggle(false);
      $('#sortmenu').toggle(false);
    });

  $('<a>Sort</a>').appendTo(box)
    .click(function() {
      $('#filtermenu').toggle(false);
      $('#exportmenu').toggle(false);
      $('#zoommenu').toggle(false);
      $('#imagemenu').toggle(false);
      $('#sortmenu').toggle();
    });


  if (el.tbl.imageCells.length > 0) //We have some image links in this table
    $('<a>Images</a>').appendTo(box)
    .click(function() {
      $('#exportmenu').toggle(false);
      $('#zoommenu').toggle(false);
      $('#filtermenu').toggle(false);
      $('#imagemenu').toggle();
    });


  function setZoom(zoom) {
    tableEl.style.fontSize = zoom + '%';
  }

  function copyVisibleTable() {
    var nt=$(tableEl).clone()[0];
    //first remove any explicit line breaks - thats not helpoing us with a grid!
    $(nt).find('br').remove();
    //also links aren't great for us
    $(nt).find('a').remove();
    //now remove any filtered rows from the table
    $(nt).find('tr.filtered').remove();
    return nt;
  }

  //Add exports section
  var expm = $('<div id=exportmenu class=menu>').appendTo(box).hide();
  $('<span>Download </span>').appendTo(expm);

  $('<a>as CSV</a>').prop({
      'download': "results.csv"
    })
    .appendTo(expm)
    .click(function() {
      return ExcellentExport.csv(this, copyVisibleTable());
    });
  $('<a>as XLS</a>').prop({
      'download': "results.xls"
    })
    .appendTo(expm)
    .click(function() {
      return ExcellentExport.excel(this, copyVisibleTable(), "Exported Results");
    });
  $('<a>as PDF</a>').appendTo(expm)
    .click(function() {
      var pdf = new jsPDF('p', 'pt', 'letter');
       source = $('<div>').append(copyVisibleTable())[0];
       specialElementHandlers = {  };
       margins = {
           top: 80,
           bottom: 60,
           left: 10,
           width: 700
       };
       pdf.fromHTML(
         source, // HTML string or DOM elem ref.
         margins.left, // x coord
         margins.top, { // y coord
           'width': margins.width, // max width of content on PDF
           'elementHandlers': specialElementHandlers
         },
         function (dispose) {
           pdf.save('result.pdf');
         }, margins);
       return true;
    });

  //Add Zoom Menu
  var zm = $('<div id=zoommenu class=menu>').appendTo(box).hide();
  $('<span>Zoom</span>').appendTo(zm);
  $('<a>120%</a>').appendTo(zm).click(function() {
    setZoom(120);
  });
  $('<a>100%</a>').appendTo(zm).click(function() {
    setZoom(100);
  });
  $('<a>80%</a>').appendTo(zm).click(function() {
    setZoom(80);
  });
  $('<a>50%</a>').appendTo(zm).click(function() {
    setZoom(50);
  });

  //Add filter menu
  var filt = $('<div id=filtermenu class=menu>').appendTo(box).hide();
  el.tbl.filterPrompt = $('<span>Filters: (none)</span>').appendTo(filt);
  $('<a>Add filter</a>').appendTo(filt).click(function() {
    showFilterMenu(el)
  });
  $('<a>Clear filters</a>').appendTo(filt).click(function() {
     el.tbl.filters=[];
     el.tbl.filterData();
  });

  el.tbl.filterData=function() //actual filtering code
  {
    //go though each row and figure out if it is in or out
    for (var i = 1; i < el.tbl.tableEl.rows.length; i += 1) {
      var row=el.tbl.tableEl.rows[i];
      var isIn=true;
      //go through each row
      for (var j=0;j<el.tbl.filters.length;j+=1) {
        var filt=el.tbl.filters[j];
        //okay for this filter get the value
        var val=row.cells[filt.column].textContent;
        if (filt.options.indexOf(val)<0) {
          isIn=false;
          break;
        }
      }
      row.classList.toggle('filtered',!isIn);
      el.tbl.rowFiltered[i]=!isIn;
    }
    //set up the prompt:
    var prompt="";
    for (var j=0;j<el.tbl.filters.length;j+=1) {
      var filt=el.tbl.filters[j];
      prompt+="<span class=res>"+el.tbl.headerCells[filt.column].textContent+": "+filt.options.join(" OR ")+"</span>&nbsp;";
    }
    if (!prompt) prompt="(none)";
    el.tbl.filterPrompt.html("Filters:"+ prompt);
  }

  //Add sort menu
  var sort = $('<div id=sortmenu class=menu>').appendTo(box).hide();
  el.tbl.sortPrompt = $('<span>Sort: (default)</span>').appendTo(sort);
  for (var i=0;i<el.tbl.headerCells.length;i+=1) {
      $('<a>').addClass('item')
              .text(el.tbl.headerCells[i].innerHTML)
              .click(makeSetSort(el,i))
              .appendTo(sort);
  }


  //Add Image Menu
  var im = $('<div id=imagemenu class=menu>').appendTo(box).hide();
  el.tbl.imageLoadPrompt = $('<span>Images (not loaded)</span>').appendTo(im);
  $('<a>Show Thumbnails</a>').appendTo(im).click(function() {
    showImages(el.tbl, true);
  });
  $('<a>Show Tiles</a>').appendTo(im).click(function() {
    showImages(el.tbl, false);
  });
  $('<a>Download as Zip</a>').appendTo(im).click(function() {
    zipImages(el.tbl);
  });
  updateImagePrompts(el.tbl);

  box.prependTo(el);

  //we should also wrap the table
  var wrapper = $('<div>').addClass('resulttableholder');
  $(tableEl).wrap(wrapper);
};

function makeSetSort(el,i)
{
  return function() {
    //resort the table according to this prompt
    if (el.tbl.sortCol==i)
      el.tbl.sortAsc=!el.tbl.sortAsc;
    el.tbl.sortCol=i;
    el.tbl.sortPrompt.text("Sort: "+el.tbl.headerCells[i].innerHTML+((el.tbl.sortAsc)?" Ascending":" Descending"));
    sortTable(el);
    return true;
  }
}

function sortTable(el)
{
    var tbody = $(el.tbl.tableEl).find('tbody');
    tbody.find('tr').sort(function(a, b) {
      var selector='td:nth-child('+(el.tbl.sortCol+1)+')';
      var da=$(selector, a).text();
      var db=$(selector, b).text();
      var result=0;
      switch (el.tbl.colTypes[el.tbl.sortCol]) {
        case 3:
          da=Date.parse(da);
          db=Date.parse(db);
          result=((da==db)?0:((da>db)?1:-1));
          break;
        case 5:
          da=Number(da);
          db=Number(db);
          result=((da==db)?0:((da>db)?1:-1));
          break;
        default:
          result=da.localeCompare(db);
          break;
      }

      if (!el.tbl.sortAsc) result*=-1;
      return result;
    }).appendTo(tbody);
}

function getDataType(data)
{
  //if we can make it a number return 5
  if (!isNaN(data)) return 5;
  //if we can make it a date return 3
  if (!isNaN(Date.parse(data))) return 3;
  //otherwise assume string compare and return 0
  return 0;
}
function analyseTableContent(el, tbl) {
  el.tbl = {
    loaded: 0,
    imageCells: [],
    tableEl: tbl,
    filters: [],
    rowFiltered: [],
    sortAsc: true,
    sortCol: -1,
    colTypes: []
  }
  //go thro each column at make the width good
  var header = tbl.rows[0];
  el.tbl.headerCells=header.cells;
  var tblwidth = 1;
  var hfact = 1.1;
  for (var i = 0; i < header.cells.length; i += 1) {
    var max = 0,tot = 0;
    var maxType=99;
    //go down the column
    for (var j = 0; j < tbl.rows.length; j += 1) {
      var cell = tbl.rows[j].cells[i];
      if (!cell) continue;
      var text=cell.innerText;
      //kill anything after a line break - thats not helping us for types or length
      var flb=text.indexOf('\n');
      if (flb>=3)
        text=text.substring(0,flb);

      var type=getDataType(text);
      if (type<maxType) maxType=type; //fail downwards in type list

      var length = text.length;
      if (length > max) max = length;
      tot += length;
    }
    el.tbl.colTypes[i]=maxType;
    console.log("maxtype:"+maxType);
    var avg = tot / tbl.rows.length;
    if (max > 20) max = 20;
    header.cells[i].style.width = (max * hfact) + 'ch';

    tblwidth += max;
  }
  tbl.style.width = (tblwidth * hfact) + 'ch';

  //go through the every cell in the table looking for images
  for (var i = 0; i < tbl.rows.length; i += 1)
    for (var j = 0; j < tbl.rows[i].cells.length; j += 1) {
      var cell = tbl.rows[i].cells[j];
      $(cell).find('a').each(function(ind, lnk) {
        var href = lnk.href;
        if ((href.indexOf('.png') > 0) || (href.indexOf('.jpg') > 0)) {
          el.tbl.imageCells.push({
            row: i,
            column: j,
            cellEl: $(cell),
            linkEl: $(lnk),
            href: href,
            imgEl: null
          })

        }
      });
    }
}

function showImages(tbl, thumb) {
  for (var i = 0; i < tbl.imageCells.length; i += 1) {
    var item = tbl.imageCells[i];
    if (item.imgEl == null)
      createImg(tbl, item);
    item.imgEl.toggleClass('thumbnail', thumb);
    item.imgEl.toggleClass('tile', !thumb);
  }
}

function createImg(tbl, item) {
  item.imgEl = $("<img>").attr('src', item.href);
  item.loaded = false;
  item.imgEl.one('load', function() {
    item.loaded = true;
    updateImagePrompts(tbl)
  });
  item.imgEl.addClass('resulttableimage');
  item.cellEl.append(item.imgEl);
}

function updateImagePrompts(tbl) {
  var loaded = 0;
  for (var i = 0; i < tbl.imageCells.length; i += 1)
    if (tbl.imageCells[i].loaded) loaded += 1;

  tbl.imageLoadPrompt.text("Images (" + loaded + "/" + tbl.imageCells.length + ")");
  tbl.loaded = loaded;
}


//Filtering information
function showFilterMenu(top)
{
  if (top.sidebar) top.sidebar.remove()
  var main=$('<div>').addClass('ressidebar');
  top.sidebar=main;
  $('<h1>').text('Filter by').appendTo(main);
  for (var i=0;i<top.tbl.headerCells.length;i+=1) {
      $('<a>').addClass('item')
              .text(top.tbl.headerCells[i].innerHTML)
              .click(makeFilterSelect(top,i))
              .appendTo(main);
  }
  $('<a>').addClass('action').text('Cancel').appendTo(main).click(function() {top.sidebar.remove()});
  main.appendTo(top);
}

function makeFilterSelect(top,i)
{
  return function() {
    showFilterOptions(top,i)
  }
}

function loadFilterValues(top,col)
{
  var vals=[];
  //go through each cell in the column
  for (var i = 1; i < top.tbl.tableEl.rows.length; i += 1) {
    val=top.tbl.tableEl.rows[i].cells[col].textContent;
    if (vals.indexOf(val)<0) vals.push(val);
    if (vals.length>50) break;
  }
  return vals;
}

function showFilterOptions(top,col)
{
  if (top.sidebar) top.sidebar.remove()
  var main=$('<div>').addClass('ressidebar');
  top.sidebar=main;
  $('<h1>').text('Choose Options').appendTo(main);
  var vals=loadFilterValues(top,col);
  for (var i=0;i<vals.length;i+=1) {
    var box=$('<div>').addClass('item').appendTo(main);
    $('<input type=checkbox>').appendTo(box);
    $('<label>').addClass('item').text(vals[i]).appendTo(box);
  }
  $('<a>').addClass('action').text('Apply Filter').appendTo(main).click(makeFilterHandler(top,col,main));
  $('<a>').addClass('action').text('Cancel').appendTo(main).click(function() {top.sidebar.remove()});
  main.appendTo(top);
}


function makeFilterHandler(top,col,ops)
{
  return function() {
    var filter={
        column: col,
        options: []
    }
    //go through all the options
    ops.find('input:checked').each(function (ind,el){
      filter.options.push(el.nextSibling.textContent);
    })

    top.tbl.filters.push(filter);
    top.sidebar.remove();
    top.tbl.filterData();
  }
}



//support function for zip functionality
//**************************************
function urlToPromise(url) {
  return new Promise(function(resolve, reject) {
    JSZipUtils.getBinaryContent(url, function(err, data) {
      if (err) {
        console.log("Couldn't source data for " + url + ": " + err);
        resolve("---"); //return null data instead
      } else {
        console.log(data);
        if (!(data instanceof ArrayBuffer)) {
          console.log("URL response wasn't byte-like on " + url);
          resolve("---"); //return null data instead
        }
        resolve(data);
      }
    });
  });
}

function zipImages(tbl) {
  var zip = new JSZip();
  for (var i = 0; i < tbl.imageCells.length; i += 1) {
    var item = tbl.imageCells[i];
    var href = item.href;
    var filename = "col_" + item.column + "_" + item.row;
    if (tbl.rowFiltered[item.row]) continue; //check if row is disabled
    if (href.indexOf('.png') > 0) filename += ".png";
    else if (href.indexOf('.jpg') > 0) filename += ".jpg";
    zip.file(filename, urlToPromise(href), {
      binary: true
    });
  }

  zip.generateAsync({
      type: "blob"
    }, function updateCallback(metadata) {
      var msg = "Building Zip File : " + metadata.percent.toFixed(2) + " %";
      tbl.imageLoadPrompt.text(msg);
    })
    .then(function callback(blob) {
      saveAs(blob, "result_images.zip");
    }, function(e) {
      alert("Failed building Zip: " + e);
    });
  updateImagePrompts(tbl);
}

//Zip utils for cross browser solutions:
var JSZipUtils = {};
JSZipUtils._getBinaryFromXHR = function(xhr) {
  return xhr.response || xhr.responseText;
};

// taken from jQuery
function createStandardXHR() {
  try {
    return new window.XMLHttpRequest();
  } catch (e) {}
}

function createActiveXHR() {
  try {
    return new window.ActiveXObject("Microsoft.XMLHTTP");
  } catch (e) {}
}

var createXHR = window.ActiveXObject ? function() {
    return createStandardXHR() || createActiveXHR();
  } :
  createStandardXHR;


JSZipUtils.getBinaryContent = function(path, callback) {
  try {
    var xhr = createXHR();

    xhr.open('GET', path, true);

    // recent browsers
    if ("responseType" in xhr) {
      xhr.responseType = "arraybuffer";
    }

    // older browser
    if (xhr.overrideMimeType) {
      xhr.overrideMimeType("text/plain; charset=x-user-defined");
    }

    xhr.onreadystatechange = function(evt) {
      var file, err;
      // use `xhr` and not `this`... thanks IE
      if (xhr.readyState === 4) {
        if (xhr.status === 200 || xhr.status === 0) {
          file = null;
          err = null;
          try {
            file = JSZipUtils._getBinaryFromXHR(xhr);
          } catch (e) {
            err = new Error(e);
          }
          callback(err, file);
        } else {
          callback(new Error("Ajax error for " + path + " : " + this.status + " " + this.statusText), null);
        }
      }
    };

    xhr.send();

  } catch (e) {
    callback(new Error(e), null);
  }
};


//browser independent file saver!

var saveAs = saveAs || (function(view) {
  "use strict";
  // IE <10 is explicitly unsupported
  if (typeof view === "undefined" || typeof navigator !== "undefined" && /MSIE [1-9]\./.test(navigator.userAgent)) {
    return;
  }
  var
    doc = view.document
    // only get URL when necessary in case Blob.js hasn't overridden it yet
    ,
    get_URL = function() {
      return view.URL || view.webkitURL || view;
    },
    save_link = doc.createElementNS("http://www.w3.org/1999/xhtml", "a"),
    can_use_save_link = "download" in save_link,
    click = function(node) {
      var event = new MouseEvent("click");
      node.dispatchEvent(event);
    },
    is_safari = /constructor/i.test(view.HTMLElement) || view.safari,
    is_chrome_ios = /CriOS\/[\d]+/.test(navigator.userAgent),
    throw_outside = function(ex) {
      (view.setImmediate || view.setTimeout)(function() {
        throw ex;
      }, 0);
    },
    force_saveable_type = "application/octet-stream"
    // the Blob API is fundamentally broken as there is no "downloadfinished" event to subscribe to
    ,
    arbitrary_revoke_timeout = 1000 * 40 // in ms
    ,
    revoke = function(file) {
      var revoker = function() {
        if (typeof file === "string") { // file is an object URL
          get_URL().revokeObjectURL(file);
        } else { // file is a File
          file.remove();
        }
      };
      setTimeout(revoker, arbitrary_revoke_timeout);
    },
    dispatch = function(filesaver, event_types, event) {
      event_types = [].concat(event_types);
      var i = event_types.length;
      while (i--) {
        var listener = filesaver["on" + event_types[i]];
        if (typeof listener === "function") {
          try {
            listener.call(filesaver, event || filesaver);
          } catch (ex) {
            throw_outside(ex);
          }
        }
      }
    },
    auto_bom = function(blob) {
      // prepend BOM for UTF-8 XML and text/* types (including HTML)
      // note: your browser will automatically convert UTF-16 U+FEFF to EF BB BF
      if (/^\s*(?:text\/\S*|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-8/i.test(blob.type)) {
        return new Blob([String.fromCharCode(0xFEFF), blob], {
          type: blob.type
        });
      }
      return blob;
    },
    FileSaver = function(blob, name, no_auto_bom) {
      if (!no_auto_bom) {
        blob = auto_bom(blob);
      }
      // First try a.download, then web filesystem, then object URLs
      var
        filesaver = this,
        type = blob.type,
        force = type === force_saveable_type,
        object_url, dispatch_all = function() {
          dispatch(filesaver, "writestart progress write writeend".split(" "));
        }
        // on any filesys errors revert to saving with object URLs
        ,
        fs_error = function() {
          if ((is_chrome_ios || (force && is_safari)) && view.FileReader) {
            // Safari doesn't allow downloading of blob urls
            var reader = new FileReader();
            reader.onloadend = function() {
              var url = is_chrome_ios ? reader.result : reader.result.replace(/^data:[^;]*;/, 'data:attachment/file;');
              var popup = view.open(url, '_blank');
              if (!popup) view.location.href = url;
              url = undefined; // release reference before dispatching
              filesaver.readyState = filesaver.DONE;
              dispatch_all();
            };
            reader.readAsDataURL(blob);
            filesaver.readyState = filesaver.INIT;
            return;
          }
          // don't create more object URLs than needed
          if (!object_url) {
            object_url = get_URL().createObjectURL(blob);
          }
          if (force) {
            view.location.href = object_url;
          } else {
            var opened = view.open(object_url, "_blank");
            if (!opened) {
              // Apple does not allow window.open, see https://developer.apple.com/library/safari/documentation/Tools/Conceptual/SafariExtensionGuide/WorkingwithWindowsandTabs/WorkingwithWindowsandTabs.html
              view.location.href = object_url;
            }
          }
          filesaver.readyState = filesaver.DONE;
          dispatch_all();
          revoke(object_url);
        };
      filesaver.readyState = filesaver.INIT;

      if (can_use_save_link) {
        object_url = get_URL().createObjectURL(blob);
        setTimeout(function() {
          save_link.href = object_url;
          save_link.download = name;
          click(save_link);
          dispatch_all();
          revoke(object_url);
          filesaver.readyState = filesaver.DONE;
        });
        return;
      }

      fs_error();
    },
    FS_proto = FileSaver.prototype,
    saveAs = function(blob, name, no_auto_bom) {
      return new FileSaver(blob, name || blob.name || "download", no_auto_bom);
    };
  // IE 10+ (native saveAs)
  if (typeof navigator !== "undefined" && navigator.msSaveOrOpenBlob) {
    return function(blob, name, no_auto_bom) {
      name = name || blob.name || "download";

      if (!no_auto_bom) {
        blob = auto_bom(blob);
      }
      return navigator.msSaveOrOpenBlob(blob, name);
    };
  }

  FS_proto.abort = function() {};
  FS_proto.readyState = FS_proto.INIT = 0;
  FS_proto.WRITING = 1;
  FS_proto.DONE = 2;

  FS_proto.error =
    FS_proto.onwritestart =
    FS_proto.onprogress =
    FS_proto.onwrite =
    FS_proto.onabort =
    FS_proto.onerror =
    FS_proto.onwriteend =
    null;

  return saveAs;
}(
  typeof self !== "undefined" && self ||
  typeof window !== "undefined" && window ||
  this.content
));
