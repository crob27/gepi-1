//>>built
define("dojox/wire/demos/WidgetRepeater",["dijit","dojo","dojox","dojo/require!dojo/parser,dijit/_Widget,dijit/_Templated,dijit/_Container"],function(_1,_2,_3){
_2.provide("dojox.wire.demos.WidgetRepeater");
_2.require("dojo.parser");
_2.require("dijit._Widget");
_2.require("dijit._Templated");
_2.require("dijit._Container");
_2.declare("dojox.wire.demos.WidgetRepeater",[_1._Widget,_1._Templated,_1._Container],{templateString:"<div class='WidgetRepeater' dojoAttachPoint='repeaterNode'></div>",widget:null,repeater:null,createNew:function(_4){
try{
if(_2.isString(this.widget)){
this.widget=_2.getObject(this.widget);
}
this.addChild(new this.widget(_4));
this.repeaterNode.appendChild(document.createElement("br"));
}
catch(e){
}
}});
});
