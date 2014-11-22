(function(b){var a=function(b,e){var f=/[^\w\-\.:]/.test(b)?new Function(a.arg+",tmpl","var _e=tmpl.encode"+a.helper+",_s='"+b.replace(a.regexp,a.func)+"';return _s;"):a.cache[b]=a.cache[b]||a(a.load(b));return e?f(e,a):function(d){return f(d,a)}};a.cache={};a.load=function(a){return document.getElementById(a).innerHTML};a.regexp=/([\s'\\])(?![^%]*%\})|(?:\{%(=|#)([\s\S]+?)%\})|(\{%)|(%\})/g;a.func=function(a,b,f,d,i,h){if(b)return{"\n":"\\n","\r":"\\r","\t":"\\t"," ":" "}[a]||"\\"+a;if(f)return"="===
f?"'+_e("+d+")+'":"'+("+d+"||'')+'";if(i)return"';";if(h)return"_s+='"};a.encReg=/[<>&"'\x00]/g;a.encMap={"<":"&lt;",">":"&gt;","&":"&amp;",'"':"&quot;","'":"&#39;"};a.encode=function(b){return(""+(b||"")).replace(a.encReg,function(b){return a.encMap[b]||""})};a.arg="o";a.helper=",print=function(s,e){_s+=e&&(s||'')||_e(s);},include=function(s,d){_s+=tmpl(s,d);}";"function"==typeof define&&define.amd?define(function(){return a}):b.tmpl=a})(this);
(function(b){"function"===typeof define&&define.amd?define(["jquery"],b):b(window.jQuery)})(function(b){var a=0;b.ajaxTransport("iframe",function(c){if(c.async&&("POST"===c.type||"GET"===c.type)){var e,f;return{send:function(d,i){e=b('<form style="display:none;"></form>');f=b('<iframe src="javascript:false;" name="iframe-transport-'+(a+=1)+'"></iframe>').bind("load",function(){var a,d=b.isArray(c.paramName)?c.paramName:[c.paramName];f.unbind("load").bind("load",function(){var a;try{if(a=f.contents(),
!a.length||!a[0].firstChild)throw Error();}catch(d){a=void 0}i(200,"success",{iframe:a});b('<iframe src="javascript:false;"></iframe>').appendTo(e);e.remove()});e.prop("target",f.prop("name")).prop("action",c.url).prop("method",c.type);c.formData&&b.each(c.formData,function(a,f){b('<input type="hidden"/>').prop("name",f.name).val(f.value).appendTo(e)});c.fileInput&&c.fileInput.length&&"POST"===c.type&&(a=c.fileInput.clone(),c.fileInput.after(function(f){return a[f]}),c.paramName&&c.fileInput.each(function(a){b(this).prop("name",
d[a]||c.paramName)}),e.append(c.fileInput).prop("enctype","multipart/form-data").prop("encoding","multipart/form-data"));e.submit();a&&a.length&&c.fileInput.each(function(f,d){var c=b(a[f]);b(d).prop("name",c.prop("name"));c.replaceWith(d)})});e.append(f).appendTo(document.body)},abort:function(){f&&f.unbind("load").prop("src","javascript".concat(":false;"));e&&e.remove()}}}});b.ajaxSetup({converters:{"iframe text":function(a){return b(a[0].body).text()},"iframe json":function(a){return b.parseJSON(b(a[0].body).text())},
"iframe html":function(a){return b(a[0].body).html()},"iframe script":function(a){return b.globalEval(b(a[0].body).text())}}})});
(function(b){"function"===typeof define&&define.amd?define(["jquery","jquery.ui.widget"],b):b(window.jQuery)})(function(b){b.support.xhrFileUpload=!(!window.XMLHttpRequestUpload||!window.FileReader);b.support.xhrFormDataFileUpload=!!window.FormData;b.widget("blueimp.fileupload",{options:{namespace:void 0,dropZone:b(document),fileInput:void 0,replaceFileInput:!0,paramName:void 0,singleFileUploads:!0,limitMultiFileUploads:void 0,sequentialUploads:!1,limitConcurrentUploads:void 0,forceIframeTransport:!1,
redirect:void 0,redirectParamName:void 0,postMessage:void 0,multipart:!0,maxChunkSize:void 0,uploadedBytes:void 0,recalculateProgress:!0,formData:function(a){return a.serializeArray()},add:function(a,b){b.submit()},processData:!1,contentType:!1,cache:!1},_refreshOptionsList:["namespace","dropZone","fileInput","multipart","forceIframeTransport"],_isXHRUpload:function(a){return!a.forceIframeTransport&&(!a.multipart&&b.support.xhrFileUpload||b.support.xhrFormDataFileUpload)},_getFormData:function(a){var c;
return"function"===typeof a.formData?a.formData(a.form):b.isArray(a.formData)?a.formData:a.formData?(c=[],b.each(a.formData,function(a,f){c.push({name:a,value:f})}),c):[]},_getTotal:function(a){var c=0;b.each(a,function(a,f){c+=f.size||1});return c},_onProgress:function(a,b){if(a.lengthComputable){var e=b.total||this._getTotal(b.files),f=parseInt(a.loaded/a.total*(b.chunkSize||e),10)+(b.uploadedBytes||0);this._loaded+=f-(b.loaded||b.uploadedBytes||0);b.lengthComputable=!0;b.loaded=f;b.total=e;this._trigger("progress",
a,b);this._trigger("progressall",a,{lengthComputable:!0,loaded:this._loaded,total:this._total})}},_initProgressListener:function(a){var c=this,e=a.xhr?a.xhr():b.ajaxSettings.xhr();e.upload&&(b(e.upload).bind("progress",function(f){var d=f.originalEvent;f.lengthComputable=d.lengthComputable;f.loaded=d.loaded;f.total=d.total;c._onProgress(f,a)}),a.xhr=function(){return e})},_initXHRData:function(a){var c,e=a.files[0],f=a.multipart||!b.support.xhrFileUpload,d=a.paramName[0];if(!f||a.blob)a.headers=b.extend(a.headers,
{"X-File-Name":e.name,"X-File-Type":e.type,"X-File-Size":e.size}),a.blob?f||(a.contentType="application/octet-stream",a.data=a.blob):(a.contentType=e.type,a.data=e);f&&b.support.xhrFormDataFileUpload&&(a.postMessage?(c=this._getFormData(a),a.blob?c.push({name:d,value:a.blob}):b.each(a.files,function(f,b){c.push({name:a.paramName[f]||d,value:b})})):(a.formData instanceof FormData?c=a.formData:(c=new FormData,b.each(this._getFormData(a),function(a,f){c.append(f.name,f.value)})),a.blob?c.append(d,a.blob,
e.name):b.each(a.files,function(f,b){b instanceof Blob&&c.append(a.paramName[f]||d,b,b.name)})),a.data=c);a.blob=null},_initIframeSettings:function(a){a.dataType="iframe "+(a.dataType||"");a.formData=this._getFormData(a);a.redirect&&b("<a></a>").prop("href",a.url).prop("host")!==location.host&&a.formData.push({name:a.redirectParamName||"redirect",value:a.redirect})},_initDataSettings:function(a){if(this._isXHRUpload(a)){if(this._chunkedUpload(a,!0)||(a.data||this._initXHRData(a),this._initProgressListener(a)),
a.postMessage)a.dataType="postmessage "+(a.dataType||"")}else this._initIframeSettings(a,"iframe")},_getParamName:function(a){var c=b(a.fileInput),e=a.paramName;e?b.isArray(e)||(e=[e]):(e=[],c.each(function(){for(var a=b(this),d=a.prop("name")||"files[]",a=(a.prop("files")||[1]).length;a;)e.push(d),a-=1}),e.length||(e=[c.prop("name")||"files[]"]));return e},_initFormSettings:function(a){if(!a.form||!a.form.length)a.form=b(a.fileInput.prop("form"));a.paramName=this._getParamName(a);a.url||(a.url=a.form.prop("action")||
location.href);a.type=(a.type||a.form.prop("method")||"").toUpperCase();"POST"!==a.type&&"PUT"!==a.type&&(a.type="POST")},_getAJAXSettings:function(a){a=b.extend({},this.options,a);this._initFormSettings(a);this._initDataSettings(a);return a},_enhancePromise:function(a){a.success=a.done;a.error=a.fail;a.complete=a.always;return a},_getXHRPromise:function(a,c,e){var f=b.Deferred(),d=f.promise(),c=c||this.options.context||d;!0===a?f.resolveWith(c,e):!1===a&&f.rejectWith(c,e);d.abort=f.promise;return this._enhancePromise(d)},
_chunkedUpload:function(a,c){var e=this,f=a.files[0],d=f.size,i=a.uploadedBytes=a.uploadedBytes||0,h=a.maxChunkSize||d,g=f.webkitSlice||f.mozSlice||f.slice,j,k;if(!this._isXHRUpload(a)||!g||!(i||h<d)||a.data)return!1;if(c)return!0;if(i>=d)return f.error="uploadedBytes",this._getXHRPromise(!1,a.context,[null,"error",f.error]);d=Math.ceil((d-i)/h);j=function(d){return!d?e._getXHRPromise(!0,a.context):j(d-=1).pipe(function(){var c=b.extend({},a);c.blob=g.call(f,i+d*h,i+(d+1)*h);c.chunkSize=c.blob.size;
e._initXHRData(c);e._initProgressListener(c);return k=(b.ajax(c)||e._getXHRPromise(!1,c.context)).done(function(){c.loaded||e._onProgress(b.Event("progress",{lengthComputable:!0,loaded:c.chunkSize,total:c.chunkSize}),c);a.uploadedBytes=c.uploadedBytes+=c.chunkSize})})};d=j(d);d.abort=function(){return k.abort()};return this._enhancePromise(d)},_beforeSend:function(a,b){0===this._active&&this._trigger("start");this._active+=1;this._loaded+=b.uploadedBytes||0;this._total+=this._getTotal(b.files)},_onDone:function(a,
c,e,f){this._isXHRUpload(f)||this._onProgress(b.Event("progress",{lengthComputable:!0,loaded:1,total:1}),f);f.result=a;f.textStatus=c;f.jqXHR=e;this._trigger("done",null,f)},_onFail:function(a,b,e,f){f.jqXHR=a;f.textStatus=b;f.errorThrown=e;this._trigger("fail",null,f);f.recalculateProgress&&(this._loaded-=f.loaded||f.uploadedBytes||0,this._total-=f.total||this._getTotal(f.files))},_onAlways:function(a,b,e,f){this._active-=1;f.textStatus=b;e&&e.always?(f.jqXHR=e,f.result=a):(f.jqXHR=a,f.errorThrown=
e);this._trigger("always",null,f);0===this._active&&(this._trigger("stop"),this._loaded=this._total=0)},_onSend:function(a,c){var e=this,f,d,i,h=e._getAJAXSettings(c),g=function(d,c){e._sending+=1;return f=f||(!1!==d&&!1!==e._trigger("send",a,h)&&(e._chunkedUpload(h)||b.ajax(h))||e._getXHRPromise(!1,h.context,c)).done(function(a,f,b){e._onDone(a,f,b,h)}).fail(function(a,f,b){e._onFail(a,f,b,h)}).always(function(a,f,b){e._sending-=1;e._onAlways(a,f,b,h);if(h.limitConcurrentUploads&&h.limitConcurrentUploads>
e._sending)for(a=e._slots.shift();a;){if(!a.isRejected()){a.resolve();break}a=e._slots.shift()}})};this._beforeSend(a,h);return this.options.sequentialUploads||this.options.limitConcurrentUploads&&this.options.limitConcurrentUploads<=this._sending?(1<this.options.limitConcurrentUploads?(d=b.Deferred(),this._slots.push(d),i=d.pipe(g)):i=this._sequence=this._sequence.pipe(g,g),i.abort=function(){var a=[void 0,"abort","abort"];if(!f){d&&d.rejectWith(a);return g(false,a)}return f.abort()},this._enhancePromise(i)):
g()},_onAdd:function(a,c){var e=this,f=!0,d=b.extend({},this.options,c),i=d.limitMultiFileUploads,h=this._getParamName(d),g,j,k;if(!d.singleFileUploads&&!i||!this._isXHRUpload(d))j=[c.files],g=[h];else if(!d.singleFileUploads&&i){j=[];g=[];for(k=0;k<c.files.length;k+=i)j.push(c.files.slice(k,k+i)),d=h.slice(k,k+i),d.length||(d=h),g.push(d)}else g=h;c.originalFiles=c.files;b.each(j||c.files,function(d,i){var h=b.extend({},c);h.files=j?i:[i];h.paramName=g[d];h.submit=function(){return h.jqXHR=this.jqXHR=
!1!==e._trigger("submit",a,this)&&e._onSend(a,this)};return f=e._trigger("add",a,h)});return f},_normalizeFile:function(a,b){void 0===b.name&&void 0===b.size&&(b.name=b.fileName,b.size=b.fileSize)},_replaceFileInput:function(a){var c=a.clone(!0);b("<form></form>").append(c)[0].reset();a.after(c).detach();b.cleanData(a.unbind("remove"));this.options.fileInput=this.options.fileInput.map(function(b,f){return f===a[0]?c[0]:f});a[0]===this.element[0]&&(this.element=c)},_onChange:function(a){var c=a.data.fileupload,
e={files:b.each(b.makeArray(a.target.files),c._normalizeFile),fileInput:b(a.target),form:b(a.target.form)};e.files.length||(e.files=[{name:a.target.value.replace(/^.*\\/,"")}]);c.options.replaceFileInput&&c._replaceFileInput(e.fileInput);if(!1===c._trigger("change",a,e)||!1===c._onAdd(a,e))return!1},_onPaste:function(a){var c=a.data.fileupload,e=a.originalEvent.clipboardData,f={files:[]};b.each(e&&e.items||[],function(a,b){var c=b.getAsFile&&b.getAsFile();c&&f.files.push(c)});if(!1===c._trigger("paste",
a,f)||!1===c._onAdd(a,f))return!1},_onDrop:function(a){var c=a.data.fileupload,e=a.dataTransfer=a.originalEvent.dataTransfer,e={files:b.each(b.makeArray(e&&e.files),c._normalizeFile)};if(!1===c._trigger("drop",a,e)||!1===c._onAdd(a,e))return!1;a.preventDefault()},_onDragOver:function(a){var b=a.data.fileupload,e=a.dataTransfer=a.originalEvent.dataTransfer;if(!1===b._trigger("dragover",a))return!1;e&&(e.dropEffect=e.effectAllowed="copy");a.preventDefault()},_initEventHandlers:function(){var a=this.options.namespace;
this._isXHRUpload(this.options)&&this.options.dropZone.bind("dragover."+a,{fileupload:this},this._onDragOver).bind("drop."+a,{fileupload:this},this._onDrop).bind("paste."+a,{fileupload:this},this._onPaste);this.options.fileInput.bind("change."+a,{fileupload:this},this._onChange)},_destroyEventHandlers:function(){var a=this.options.namespace;this.options.dropZone.unbind("dragover."+a,this._onDragOver).unbind("drop."+a,this._onDrop).unbind("paste."+a,this._onPaste);this.options.fileInput.unbind("change."+
a,this._onChange)},_setOption:function(a,c){var e=-1!==b.inArray(a,this._refreshOptionsList);e&&this._destroyEventHandlers();b.Widget.prototype._setOption.call(this,a,c);e&&(this._initSpecialOptions(),this._initEventHandlers())},_initSpecialOptions:function(){var a=this.options;void 0===a.fileInput?a.fileInput=this.element.is("input:file")?this.element:this.element.find("input:file"):a.fileInput instanceof b||(a.fileInput=b(a.fileInput));a.dropZone instanceof b||(a.dropZone=b(a.dropZone))},_create:function(){var a=
this.options,c=b.extend({},this.element.data());c[this.widgetName]=void 0;b.extend(a,c);a.namespace=a.namespace||this.widgetName;this._initSpecialOptions();this._slots=[];this._sequence=this._getXHRPromise(!0);this._sending=this._active=this._loaded=this._total=0;this._initEventHandlers()},destroy:function(){this._destroyEventHandlers();b.Widget.prototype.destroy.call(this)},enable:function(){b.Widget.prototype.enable.call(this);this._initEventHandlers()},disable:function(){this._destroyEventHandlers();
b.Widget.prototype.disable.call(this)},add:function(a){a&&!this.options.disabled&&(a.files=b.each(b.makeArray(a.files),this._normalizeFile),this._onAdd(null,a))},send:function(a){return a&&!this.options.disabled&&(a.files=b.each(b.makeArray(a.files),this._normalizeFile),a.files.length)?this._onSend(null,a):this._getXHRPromise(!1,a&&a.context)}})});
(function(b){"function"===typeof define&&define.amd?define(["jquery","tmpl","load-image","./jquery.fileupload-ip"],b):b(window.jQuery,window.tmpl,window.loadImage)})(function(b,a,c){var e=(b.blueimpIP||b.blueimp).fileupload;b.widget("blueimpUI.fileupload",e,{options:{autoUpload:!1,maxNumberOfFiles:void 0,maxFileSize:void 0,minFileSize:void 0,acceptFileTypes:/.+$/i,previewSourceFileTypes:/^image\/(gif|jpeg|png)$/,previewSourceMaxFileSize:5E6,previewMaxWidth:80,previewMaxHeight:80,previewAsCanvas:!0,
uploadTemplateId:"template-upload",downloadTemplateId:"template-download",dataType:"json",add:function(a,d){var c=b(this).data("fileupload"),e=c.options,g=d.files;b(this).fileupload("resize",d).done(d,function(){c._adjustMaxNumberOfFiles(-g.length);d.isAdjusted=!0;d.files.valid=d.isValidated=c._validate(g);d.context=c._renderUpload(g).appendTo(e.filesContainer).data("data",d);c._renderPreviews(g,d.context);c._forceReflow(d.context);c._transition(d.context).done(function(){!1!==c._trigger("added",
a,d)&&(e.autoUpload||d.autoUpload)&&!1!==d.autoUpload&&d.isValidated&&d.submit()})})},send:function(a,d){var c=b(this).data("fileupload");if(!d.isValidated&&(d.isAdjusted||c._adjustMaxNumberOfFiles(-d.files.length),!c._validate(d.files)))return!1;d.context&&d.dataType&&"iframe"===d.dataType.substr(0,6)&&d.context.find(".progress").addClass(!b.support.transition&&"progress-animated").find(".bar").css("width","100%");return c._trigger("sent",a,d)},done:function(a,d){var c=b(this).data("fileupload"),
e;d.context?d.context.each(function(g){var j=b.isArray(d.result)&&d.result[g]||{error:"emptyResult"};j.error&&c._adjustMaxNumberOfFiles(1);c._transition(b(this)).done(function(){var g=b(this);e=c._renderDownload([j]).css("height",g.height()).replaceAll(g);c._forceReflow(e);c._transition(e).done(function(){d.context=b(this);c._trigger("completed",a,d)})})}):(e=c._renderDownload(d.result).appendTo(c.options.filesContainer),c._forceReflow(e),c._transition(e).done(function(){d.context=b(this);c._trigger("completed",
a,d)}))},fail:function(a,d){var c=b(this).data("fileupload"),e;c._adjustMaxNumberOfFiles(d.files.length);d.context?d.context.each(function(g){if("abort"!==d.errorThrown){var j=d.files[g];j.error=j.error||d.errorThrown||!0;c._transition(b(this)).done(function(){var g=b(this);e=c._renderDownload([j]).replaceAll(g);c._forceReflow(e);c._transition(e).done(function(){d.context=b(this);c._trigger("failed",a,d)})})}else c._transition(b(this)).done(function(){b(this).remove();c._trigger("failed",a,d)})}):
"abort"!==d.errorThrown?(c._adjustMaxNumberOfFiles(-d.files.length),d.context=c._renderUpload(d.files).appendTo(c.options.filesContainer).data("data",d),c._forceReflow(d.context),c._transition(d.context).done(function(){d.context=b(this);c._trigger("failed",a,d)})):c._trigger("failed",a,d)},progress:function(a,b){b.context&&b.context.find(".progress .bar").css("width",parseInt(100*(b.loaded/b.total),10)+"%")},progressall:function(a,d){b(this).find(".fileupload-buttonbar .progress .bar").css("width",
parseInt(100*(d.loaded/d.total),10)+"%")},start:function(a){var d=b(this).data("fileupload");d._transition(b(this).find(".fileupload-buttonbar .progress")).done(function(){d._trigger("started",a)})},stop:function(a){var d=b(this).data("fileupload");d._transition(b(this).find(".fileupload-buttonbar .progress")).done(function(){b(this).find(".bar").css("width","0%");d._trigger("stopped",a)})},destroy:function(a,d){var c=b(this).data("fileupload");d.url&&b.ajax(d);c._adjustMaxNumberOfFiles(1);c._transition(d.context).done(function(){b(this).remove();
c._trigger("destroyed",a,d)})}},_enableDragToDesktop:function(){var a=b(this),d=a.prop("href"),c=a.prop("download");a.bind("dragstart",function(a){try{a.originalEvent.dataTransfer.setData("DownloadURL",["application/octet-stream",c,d].join(":"))}catch(b){}})},_adjustMaxNumberOfFiles:function(a){"number"===typeof this.options.maxNumberOfFiles&&(this.options.maxNumberOfFiles+=a,1>this.options.maxNumberOfFiles?this._disableFileInputButton():this._enableFileInputButton())},_formatFileSize:function(a){return"number"!==
typeof a?"":1E9<=a?(a/1E9).toFixed(2)+" GB":1E6<=a?(a/1E6).toFixed(2)+" MB":(a/1E3).toFixed(2)+" KB"},_hasError:function(a){return a.error?a.error:0>this.options.maxNumberOfFiles?"maxNumberOfFiles":!this.options.acceptFileTypes.test(a.type)&&!this.options.acceptFileTypes.test(a.name)?"acceptFileTypes":this.options.maxFileSize&&a.size>this.options.maxFileSize?"maxFileSize":"number"===typeof a.size&&a.size<this.options.minFileSize?"minFileSize":null},_validate:function(a){var d=this,c=!!a.length;b.each(a,
function(a,b){b.error=d._hasError(b);b.error&&(c=!1)});return c},_renderTemplate:function(a,d){if(!a)return b();var c=a({files:d,formatFileSize:this._formatFileSize,options:this.options});return c instanceof b?c:b(this.options.templatesContainer).html(c).children()},_renderPreview:function(a,d){var e=this,h=this.options,g=b.Deferred();return(c&&c(a,function(a){d.append(a);e._forceReflow(d);e._transition(d).done(function(){g.resolveWith(d)});b.contains(document.body,d[0])||g.resolveWith(d)},{maxWidth:h.previewMaxWidth,
maxHeight:h.previewMaxHeight,canvas:h.previewAsCanvas})||g.resolveWith(d))&&g},_renderPreviews:function(a,d){var c=this,e=this.options;d.find(".preview span").each(function(d,j){var k=a[d];if(e.previewSourceFileTypes.test(k.type)&&("number"!==b.type(e.previewSourceMaxFileSize)||k.size<e.previewSourceMaxFileSize))c._processingQueue=c._processingQueue.pipe(function(){var a=b.Deferred();c._renderPreview(k,b(j)).done(function(){a.resolveWith(c)});return a.promise()})});return this._processingQueue},_renderUpload:function(a){return this._renderTemplate(this.options.uploadTemplate,
a)},_renderDownload:function(a){return this._renderTemplate(this.options.downloadTemplate,a).find("a[download]").each(this._enableDragToDesktop).end()},_startHandler:function(a){a.preventDefault();var a=b(this),d=a.closest(".template-upload").data("data");d&&d.submit&&!d.jqXHR&&d.submit()&&a.prop("disabled",!0)},_cancelHandler:function(a){a.preventDefault();var d=b(this).closest(".template-upload").data("data")||{};d.jqXHR?d.jqXHR.abort():(d.errorThrown="abort",a.data.fileupload._trigger("fail",a,
d))},_deleteHandler:function(a){a.preventDefault();var d=b(this);a.data.fileupload._trigger("destroy",a,{context:d.closest(".template-download"),url:d.attr("data-url"),type:d.attr("data-type")||"DELETE",dataType:a.data.fileupload.options.dataType})},_forceReflow:function(a){this._reflow=b.support.transition&&a.length&&a[0].offsetWidth},_transition:function(a){var d=b.Deferred();b.support.transition&&a.hasClass("fade")?a.bind(b.support.transition.end,function(c){c.target===a[0]&&(a.unbind(b.support.transition.end),
d.resolveWith(a))}).toggleClass("in"):(a.toggleClass("in"),d.resolveWith(a));return d},_initButtonBarEventHandlers:function(){var a=this.element.find(".fileupload-buttonbar"),d=this.options.filesContainer,c=this.options.namespace;a.find(".start").bind("click."+c,function(a){a.preventDefault();d.find(".start button").click()});a.find(".cancel").bind("click."+c,function(a){a.preventDefault();d.find(".cancel button").click()});a.find(".delete").bind("click."+c,function(b){b.preventDefault();d.find(".delete input:checked").siblings("button").click();
a.find(".toggle").prop("checked",!1)});a.find(".toggle").bind("change."+c,function(){d.find(".delete input").prop("checked",b(this).is(":checked"))})},_destroyButtonBarEventHandlers:function(){this.element.find(".fileupload-buttonbar button").unbind("click."+this.options.namespace);this.element.find(".fileupload-buttonbar .toggle").unbind("change."+this.options.namespace)},_initEventHandlers:function(){e.prototype._initEventHandlers.call(this);var a={fileupload:this};this.options.filesContainer.delegate(".start button",
"click."+this.options.namespace,a,this._startHandler).delegate(".cancel button","click."+this.options.namespace,a,this._cancelHandler).delegate(".delete button","click."+this.options.namespace,a,this._deleteHandler);this._initButtonBarEventHandlers()},_destroyEventHandlers:function(){var a=this.options;this._destroyButtonBarEventHandlers();a.filesContainer.undelegate(".start button","click."+a.namespace).undelegate(".cancel button","click."+a.namespace).undelegate(".delete button","click."+a.namespace);
e.prototype._destroyEventHandlers.call(this)},_enableFileInputButton:function(){this.element.find(".fileinput-button input").prop("disabled",!1).parent().removeClass("disabled")},_disableFileInputButton:function(){this.element.find(".fileinput-button input").prop("disabled",!0).parent().addClass("disabled")},_initTemplates:function(){var b=this.options;b.templatesContainer=document.createElement(b.filesContainer.prop("nodeName"));if(a&&(b.uploadTemplateId&&(b.uploadTemplate=a(b.uploadTemplateId)),
b.downloadTemplateId))b.downloadTemplate=a(b.downloadTemplateId)},_initFilesContainer:function(){var a=this.options;void 0===a.filesContainer?a.filesContainer=this.element.find(".files"):a.filesContainer instanceof b||(a.filesContainer=b(a.filesContainer))},_initSpecialOptions:function(){e.prototype._initSpecialOptions.call(this);this._initFilesContainer();this._initTemplates()},_create:function(){e.prototype._create.call(this);this._refreshOptionsList.push("filesContainer","uploadTemplateId","downloadTemplateId");
b.blueimpIP||(this._processingQueue=b.Deferred().resolveWith(this).promise(),this.resize=function(){return this._processingQueue})},enable:function(){e.prototype.enable.call(this);this.element.find("input, button").prop("disabled",!1);this._enableFileInputButton()},disable:function(){this.element.find("input, button").prop("disabled",!0);this._disableFileInputButton();e.prototype.disable.call(this)}})});
window.locale={fileupload:{errors:{maxFileSize:"Fisier prea mare",minFileSize:"Fisier prea mic",acceptFileTypes:"Extensia nu e permisa",maxNumberOfFiles:"Numar maxim de fisiere depasit",uploadedBytes:"Dimensiune maxima upload depasita",emptyResult:"Eroare upload fisier"},error:"Eroare",start:"Upload",cancel:"Anuleaza",destroy:"Sterge"}};
$(function(){$("#fileupload").fileupload();$("#fileupload").fileupload("option","redirect",window.location.href.replace(/\/[^\/]*$/,"/cors/result.html?%s"));"blueimp.github.com"===window.location.hostname?($("#fileupload").fileupload("option",{url:"//jquery-file-upload.appspot.com/",maxFileSize:5E6,acceptFileTypes:/(\.|\/)(gif|jpe?g|png)$/i,resizeMaxWidth:1920,resizeMaxHeight:1200}),$.support.cors&&$.ajax({url:"//jquery-file-upload.appspot.com/",type:"HEAD"}).fail(function(){$('<span class="alert alert-error"/>').text("Upload server currently unavailable - "+
new Date).appendTo("#fileupload")})):$("#fileupload").each(function(){var b=this;$.getJSON(this.action,function(a){a&&a.length&&$(b).fileupload("option","done").call(b,null,{result:a})})})});