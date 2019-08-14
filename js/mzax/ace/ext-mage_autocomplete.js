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
 * @version     0.4.3
 * @category    Mzax
 * @package     Mzax_Emarketing
 * @author      Jacob Siefer (jacob@mzax.de)
 * @copyright   Copyright (c) 2015 Jacob Siefer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

ace.define("ace/ext/mage_autocomplete",["require","exports","module","ace/ext/language_tools"],function(a){"use strict";function b(a){return function(b,c){var d=c.match(/\{js:\s*([a-zA-Z0-9]+)\}/);if(d){var e=b[d[1]]||window[d[1]];e&&(e.call(b),c="")}return a.call(this,b,c)}}var c=ace.require("ace/autocomplete/util"),d=ace.require("ace/snippets").snippetManager,e=a("ace/commands/default_commands").commands,f=ace.require("ace/ext/language_tools"),g=a("ace/autocomplete").Autocomplete,h=a("ace/editor").Editor,i=a("ace/config"),j=/[a-zA-Z_0-9\$\.\u00A2-\uFFFF-]/;h.prototype.setMageSnippets=function(a){a&&(this._mageSnippets=a,a.forEach(function(a){d.register([{name:"$"+a.shortcut,score:100,snippet:a.snippet,content:a.snippet,type:"snippet",trigger:"\\$(?:"+a.shortcut+")",guard:".*",scope:"mage",endTrigger:"",endGuard:".*"}]),a.type="snippet",a.score=1e3,a.meta="Mage",a.identifierRegex=j;var b=[];a.title&&b.push("<strong>",a.title,"</strong><hr></hr>"),a.shortcut&&b.push('Shortcut: <code style="font-weight:bold; color:#CB6D12">$',a.shortcut,"</code>"),
a.description&&b.push("<p>",a.description,"</p>"),a.snippet&&b.push("Code: <p><code>",a.snippet,"</code></p>"),a.docHTML=b.join("")}))};var k=function(a){var b=a.editor,d=b.completer&&b.completer.activated;if("insertstring"===a.command.name||"mage"===a.command.name||"go"==a.command.name.substr(0,2)){var e=b.getCursorPosition(),f=b.session.getLine(e.row),h=c.retrievePrecedingIdentifier(f,e.column,j);"mage."!=h||d||(b.completer||(b.completer=new g),b.completer.autoSelect=!1,b.completer.autoInsert=!1,b.completer.showPopup(b))}};f.addCompleter({getCompletions:function(a,b,c,d,e){a._mageSnippets&&e(null,a._mageSnippets)}}),i.defineOptions(h.prototype,"editor",{enableMageLiveAutocompletion:{set:function(a){a?(this.completers||(this.completers=Array.isArray(a)?a:completers),this.commands.on("afterExec",k)):this.commands.removeListener("afterExec",k)},value:!1}}),e.push({name:"mage",bindKey:{win:"Ctrl-M",mac:"Command-M"},exec:function(a){a.insert("mage.")},readOnly:!1}),["insertSnippet","insertSnippetForSelection"].forEach(function(a){
d[a]=b(d[a])})});