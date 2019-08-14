/**
 * Mzax Emarketing (www.mzax.de)
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this Extension in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @version     0.4.7
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

"use strict";!function(a){function b(a){return(a+"").toLowerCase().replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g,function(a){return a.toUpperCase()})}function c(){var b,c,d,e=[];if(a.mageSnippets)for(b=a.mageSnippets,d=0;d<b.length;d++)c=b[d],e.push([c.title||c.value,c.value]);return e}function d(b){var c,d;if(a.mageSnippets)for(c=a.mageSnippets,d=0;d<c.length;d++)if(c[d].value===b)return c[d];return null}if(!a.mzax)throw new Error("CKEDITOR.mzax is not defined");var e="/skin/adminhtml/default/default/mzax/",f="data-mage-src",g=a.mzax.ui.Placeholder;a.skin.addIcon("mage-code",e+"/images/mage_code.png",0,"34px 16px"),a.skin.addIcon("image",e+"/images/icon_image.png",0,"16px 16px"),a.skin.addIcon("source",e+"/images/icon_source.png",0,"16px 16px"),a.plugins.add("mzax_editor",{init:function(b){b.addCommand("show_source",{exec:function(b){a.owner&&b.field&&a.owner.editField(b.field)}}),b.ui.addButton("mzax_editor",{icon:"source",label:"Source",className:"mzax-cke-show-source",command:"show_source",
toolbar:"tools"})}}),a.plugins.add("mzax_image",{requires:"dialog",init:function(b){b.addCommand("mzax_image",new a.command(b,{allowedContent:"img[alt]{float,width,height}(*)",requiredContent:"img[alt,src]",contentTransformations:[["img{width}: sizeToStyle","img[width]: sizeToAttribute"],["img{float}: alignmentToStyle","img[align]: alignmentToAttribute"]],exec:function(b){var c=new a.dom.element("img");b.openDialog("mzaxImageDialog",function(a){a.on("show",function(){a.setupContent(c)}),a.on("ok",function(){a.commitContent(c),b.insertElement(c)})})}})),b.ui.addButton("mzax_image",{icon:"image",label:"Image",className:"mzax-cke-image",command:"mzax_image",toolbar:"insert"}),b.on("doubleclick",function(a){var c=a.data.element;!c.is("img")||c.data("cke-realelement")||c.isReadOnly()||(b.openDialog("mzaxImageDialog",function(a){a.on("show",function(){a.setupContent(c)}),a.on("ok",function(){a.commitContent(c)})}),a.stop())})},afterInit:function(b){b.dataProcessor.dataFilter.addRules({elements:{
img:function(c){var d=c.attributes.src;c.attributes[f]=d,a.getImagePreviewUrl&&(d=a.getImagePreviewUrl.call(b,d))&&(c.attributes.src=d)}}}),b.dataProcessor.htmlFilter.addRules({elements:{img:function(a){a.attributes.src=a.attributes[f],a.attributes["data-cke-saved-src"]&&(a.attributes["data-cke-saved-src"]=a.attributes[f]),delete a.attributes[f]}}})}}),a.plugins.add("mage_code",{requires:"widget,dialog",init:function(e){e.addCommand("insert_mage_expr",new a.dialogCommand("mageCodeDialog")),e.addCommand("insert_mage_expr",new a.command(e,{exec:function(a){a.openDialog("mageCodeDialog",function(b){b.on("show",function(){b.setupContent(c())}),b.on("ok",function(){var c=d(b.getContentElement("info","snippet").getValue());b.commitContent(c),a.insertText(c.snippet.replace(/="?\$\{[0-9]+(:(.*?))?\}"?/g,"=$2"))})})}})),e.ui.addButton("Mage",{icon:"mage-code",label:"Insert Magento Code",className:"mzax-cke-mage-expr",command:"insert_mage_expr",toolbar:"insert"}),e.widgets.add("mage_code",{dialog:"mageCodeDialog",
pathName:"paceholder",template:'<span class="mzax-paceholder">[[]]</span>',edit:function(c){var d=this.placeholder,f=[],g={title:"Magento Code",minWidth:400,minHeight:200,contents:[{id:"info",label:d.expr,title:d.expr,elements:f}]};if("else"===d.directive||d.closing)return void c.cancel();if(d.input)f.push({id:"input",type:"text",label:"Input",setup:function(a){this.setValue(a.placeholder.input)},commit:function(a){a.placeholder.input=this.getValue(),a.setData("expr",a.placeholder.render())}});else for(var h in d.params)d.params.hasOwnProperty(h)&&!function(a){f.push({id:"param_"+a,type:"text",label:b(a.replace(/[^a-z]+/gi," ").replace(/^\s*(.*?)\s*$/,"$1")),setup:function(b){this.setValue(b.placeholder.params[a])},commit:function(b){b.placeholder.params[a]=this.getValue(),b.setData("expr",b.placeholder.render())}})}(h);c.data.dialog="mageExprEdit",e._.storedDialogs&&(e._.storedDialogs[c.data.dialog]=null),a.dialog.add(c.data.dialog,function(){return g})},downcast:function(){return new a.htmlParser.text(this.placeholder.render());

},init:function(){this.setData(JSON.parse(this.element.getAttribute("expr"))),this.placeholder=new g(this.data.expr)},data:function(){this.element.setHtml(this.placeholder.toHtml(!0))}})},afterInit:function(b){b.dataProcessor.dataFilter.addRules({text:function(c,d){var e=d.parent&&a.dtd[d.parent.name];if(!e||e.span)return g.replace(c,function(c){var d=null,e=new a.htmlParser.element(c.isBlock?"div":"span");return e.add(new a.htmlParser.text(c.toHtml())),e.attributes.expr=JSON.stringify({expr:c.expr}),d=b.widgets.wrapElement(e,"mage_code"),d.getOuterHtml()})}})}}),a.dialog.add("mzaxImageDialog",function(b){function c(a){var b=a.match(/[\d.]+%?/);return b?b[0]:""}var d,e=(b.lang.placeholder,b.lang.common.generalTab);return{title:"Image",minWidth:400,minHeight:200,contents:[{id:"info",label:e,title:e,elements:[{type:"hbox",width:"100%",widths:["280px","110px"],align:"right",children:[{label:"Image Source",required:!0,type:"text",id:"src",setup:function(a){d=null,this.setValue((a.getAttribute(f)||a.getAttribute("src")).replace(/'/g,'"'));

},commit:function(c){var e=this.getValue().replace(/"/g,"'");c.setAttribute(f,e),!d&&a.getImagePreviewUrl?(d=a.getImagePreviewUrl.call(b,e))&&c.setAttribute("src",d):c.setAttribute("src",d||e)}},{style:"display:inline-block;margin-top:15px;width:100%",align:"right",type:"button",id:"browse",hidden:!a.selectImageSource,label:"Browse",onClick:function(b){var c=b.data.dialog;if(c){var e=c.getContentElement("info","src");a.selectImageSource(function(a,b){e.setValue(a),d=b||null})}}}]},{id:"alt",type:"text",style:"width: 100%;",label:"Alt",required:!0,setup:function(a){this.setValue(a.getAttribute("alt"))},commit:function(a){a.setAttribute("alt",this.getValue())}},{id:"align",type:"select",style:"width: 100%;",label:"Align",items:[["None",""],["Left","left"],["Right","right"]],setup:function(a){var b=a.getStyle("float");switch(b){case"inherit":case"none":b=""}!b&&(b=(a.getAttribute("align")||"").toLowerCase()),this.setValue(b)},commit:function(a){var b=this.getValue();b?a.setStyle("float",b):a.removeStyle("float");

}},{id:"width",type:"text",style:"width: 100%;",label:"Width",setup:function(a){this.setValue(c(a.getStyle("width")))},commit:function(b){var c=this.getValue();c?b.setStyle("width",a.tools.cssLength(c)):b.removeStyle("width")}},{id:"height",type:"text",style:"width: 100%;",label:"Height",setup:function(a){this.setValue(c(a.getStyle("height")))},commit:function(b){var c=this.getValue();c?b.setStyle("height",a.tools.cssLength(c)):b.removeStyle("height")}}]}]}}),a.dialog.add("mageCodeDialog",function(b){var e=(b.lang.placeholder,b.lang.common.generalTab),f=new a.template('<h5 class="title">{title}</h5><p class="description">{description}</p><code>{snippet}</code>');return{title:"Magento Code",minWidth:400,minHeight:200,contents:[{id:"info",label:e,title:e,elements:[{id:"snippet",type:"select",style:"width: 100%;",label:"Choose a code",className:"mzax-placeholder-snippet",required:!0,items:c(),onChange:function(){var a=this.getDialog().getContentElement("info","description"),b=d(this.getValue());

a.getElement().setHtml(f.output(b))}},{id:"description",type:"html",style:"width: 100%;",label:"Description",className:"mzax-placeholder-desc",html:"Please choose Magento code snippet",required:!1}]}]}})}(CKEDITOR);