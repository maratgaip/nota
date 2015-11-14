/*global tinyMCE, tinymce*/
/*jshint forin:true, noarg:true, noempty:true, eqeqeq:true, bitwise:true, strict:true, undef:true, unused:true, curly:true, browser:true, devel:true, maxerr:50 */
(function() {  

    "use strict";
 
    tinymce.PluginManager.add( 'bluthcodes_location', function( editor, url ) {

        editor.addButton( 'bluthcodes', {
            type: 'listbox',
            text: 'Bluthcodes\t',
            icon: false,
            onselect: function(e) {
            },
            values: [
                {
                    text: 'Accordion', 
                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[accordion]<br />[accordion-group title="Title goes here"]Text goes here[/accordion-group]<br />[accordion-group title="Title goes here"]Text goes here[/accordion-group]<br />[accordion-group title="Title goes here"]Text goes here[/accordion-group]<br />[/accordion]');  }
                },{
                    text: 'Alert', 
                    menu: [
                        {
                            text: 'Blue',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[alert style="blue"]' + tinyMCE.activeEditor.selection.getContent() + '[/alert]');  }
                        },
                        {
                            text: 'Red',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[alert style="red"]' + tinyMCE.activeEditor.selection.getContent() + '[/alert]');  }
                        },
                        {
                            text: 'Green',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[alert style="green"]' + tinyMCE.activeEditor.selection.getContent() + '[/alert]');  }
                        },
                        {
                            text: 'Yellow',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[alert style="yellow"]' + tinyMCE.activeEditor.selection.getContent() + '[/alert]');  }
                        },
                        {
                            text: 'Purple',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[alert style="purple"]' + tinyMCE.activeEditor.selection.getContent() + '[/alert]');  }
                        },
                        {
                            text: 'Dar kred',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[alert style="darkred"]' + tinyMCE.activeEditor.selection.getContent() + '[/alert]');  }
                        },
                        {
                            text: 'Brown',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[alert style="brown"]' + tinyMCE.activeEditor.selection.getContent() + '[/alert]');  }
                        },
                        {
                            text: 'Grey',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[alert style="grey"]' + tinyMCE.activeEditor.selection.getContent() + '[/alert]');  }
                        },
                        {
                            text: 'Dark',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[alert style="dark"]' + tinyMCE.activeEditor.selection.getContent() + '[/alert]');  }
                        },
                        {
                            text: 'Grass',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[alert style="grass"]' + tinyMCE.activeEditor.selection.getContent() + '[/alert]');  }
                        },
                        {
                            text: 'Pink',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[alert style="pink"]' + tinyMCE.activeEditor.selection.getContent() + '[/alert]');  }
                        },
                    ]
                },{
                    text: 'Badge', 
                    menu: [
                        {
                            text: 'Blue',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge style="blue"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                        {
                            text: 'Red',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge style="red"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                        {
                            text: 'Green',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge style="green"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                        {
                            text: 'Yellow',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge style="yellow"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                        {
                            text: 'Purple',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge style="purple"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                        {
                            text: 'Dar kred',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge style="darkred"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                        {
                            text: 'Brown',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge style="brown"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                        {
                            text: 'Grey',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge style="grey"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                        {
                            text: 'Dark',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge style="dark"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                        {
                            text: 'Grass',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge style="grass"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                        {
                            text: 'Pink',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[badge style="pink"]' + tinyMCE.activeEditor.selection.getContent() + '[/badge]');  }
                        },
                    ]
                },{
                    text: 'Button', 
                    menu: [
                        {
                            text: 'Blue',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" style="blue"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'Red',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" style="red"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'Green',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" style="green"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'Yellow',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" style="yellow"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'Purple',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" style="purple"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'Dar kred',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" style="darkred"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'Brown',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" style="brown"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'Grey',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" style="grey"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'Dark',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" style="dark"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'Grass',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" style="grass"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'Pink',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" style="pink"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'Large Button',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" size="large"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'Small Button',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" size="small"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'Mini Button',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" size="mini"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'Block Button',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" block="true"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                        {
                            text: 'New Window',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[button url="http://" target="_blank"]' + tinyMCE.activeEditor.selection.getContent() + '[/button]');  }
                        },
                    ]
                },{
                    text: 'Columns', 
                    menu: [
                        {
                            text: 'Half',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[two_first]<br /><br />[/two_first][two_second]<br /><br />[/two_second]');  }
                        },
                        {
                            text: 'Two/One',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[two_one_first]<br /><br />[/two_one_first][two_one_second]<br /><br />[/two_one_second]');  }
                        },
                        {
                            text: 'One/Two',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[one_two_first]<br /><br />[/one_two_first][one_two_second]<br /><br />[/one_two_second]');  }
                        },
                        {
                            text: 'Three',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[three_first]<br /><br />[/three_first][three_second]<br /><br />[/three_second][three_third]<br /><br />[/three_third]');  }
                        },
                        {
                            text: 'Four',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[four_first]<br /><br />[/four_first][four_second]<br /><br />[/four_second][four_third]<br /><br />[/four_third][four_fourth]<br /><br />[/four_fourth]');  }
                        },
                        {
                            text: 'One/One/Two',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[one_one_two_first]<br /><br />[/one_one_two_first][one_one_two_second]<br /><br />[/one_one_two_second][one_one_two_third]<br /><br />[/one_one_two_third]');  }
                        },
                        {
                            text: 'Two/One/One',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[two_one_one_first]<br /><br />[/two_one_one_first][two_one_one_second]<br /><br />[/two_one_one_second][two_one_one_third]<br /><br />[/two_one_one_third]');  }
                        },
                        {
                            text: 'One/Two/One',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[one_two_one_first]<br /><br />[/one_two_one_first][one_two_one_second]<br /><br />[/one_two_one_second][one_two_one_third]<br /><br />[/one_two_one_third]');  }
                        },
                    ]
                },{
                    text: 'Divider', 
                    menu: [
                        {
                            text: 'White',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[divider type="white"]');  }
                        },
                        {
                            text: 'Thin',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[divider type="thin"]');  }
                        },
                        {
                            text: 'Thick',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[divider type="thick"]');  }
                        },
                        {
                            text: 'Short',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[divider type="short"]');  }
                        },
                        {
                            text: 'Dotted',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[divider type="dotted"]');  }
                        },
                        {
                            text: 'Dashed',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[divider type="dashed"]');  }
                        },
                        {
                            text: 'Thin w/big spacing',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[divider type="thin" spacing="25"]');  }
                        },
                    ]
                },{
                    text: 'Dropcap', 
                    menu: [
                        {
                            text: 'Normal',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[dropcap]' + tinyMCE.activeEditor.selection.getContent() + '[/dropcap]');  }
                        },
                        {
                            text: 'With Background',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[dropcap background="yes"]' + tinyMCE.activeEditor.selection.getContent() + '[/dropcap]'); }
                        },
                        {
                            text: 'Custom Size',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[dropcap size="50px"]' + tinyMCE.activeEditor.selection.getContent() + '[/dropcap]'); }
                        },
                        {
                            text: 'Custom Color',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[dropcap color="#333333"]' + tinyMCE.activeEditor.selection.getContent() + '[/dropcap]'); }
                        },
                        {
                            text: 'Demo',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[dropcap background="yes" color="#333333" size="50px"]' + tinyMCE.activeEditor.selection.getContent() + '[/dropcap]'); }
                        }
                    ]
                },{
                    text: 'Label', 
                    menu: [
                        {
                            text: 'Blue',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label style="blue"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                        {
                            text: 'Red',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label style="red"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                        {
                            text: 'Green',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label style="green"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                        {
                            text: 'Yellow',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label style="yellow"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                        {
                            text: 'Purple',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label style="purple"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                        {
                            text: 'Dar kred',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label style="darkred"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                        {
                            text: 'Brown',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label style="brown"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                        {
                            text: 'Grey',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label style="grey"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                        {
                            text: 'Dark',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label style="dark"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                        {
                            text: 'Grass',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label style="grass"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                        {
                            text: 'Pink',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[label style="pink"]' + tinyMCE.activeEditor.selection.getContent() + '[/label]');  }
                        },
                    ]
                },{
                    text: 'PullQuote', 
                    menu: [
                        {
                            text: 'Normal',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[pullquote]' + tinyMCE.activeEditor.selection.getContent() + '[/pullquote]');  }
                        },
                        {
                            text: 'Right Aligned',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[pullquote align="right"]' + tinyMCE.activeEditor.selection.getContent() + '[/pullquote]');  }
                        },
                        {
                            text: 'Demo',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[pullquote align="left" background="on"]' + tinyMCE.activeEditor.selection.getContent() + '[/pullquote]');  }
                        },
                    ]
                },{
                    text: 'Syntax', 
                    menu: [
                        {
                            text: 'HTML',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[syntax type="html"]' + tinyMCE.activeEditor.selection.getContent() + '[/syntax]');  }
                        },
                        {
                            text: 'PHP',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[syntax type="php"]' + tinyMCE.activeEditor.selection.getContent() + '[/syntax]');  }
                        },
                        {
                            text: 'JS',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[syntax type="js"]' + tinyMCE.activeEditor.selection.getContent() + '[/syntax]');  }
                        },
                        {
                            text: 'CSS',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[syntax type="css"]' + tinyMCE.activeEditor.selection.getContent() + '[/syntax]');  }
                        },
                    ]
                },{
                    text: 'Tabs', 
                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[tabs-header]<br />[tabs-header-group open="one" active="yes"] HEADER ITEM [/tabs-header-group]<br />[tabs-header-group open="two"] HEADER ITEM [/tabs-header-group]<br />[/tabs-header]<br /><br />[tabs-content]<br />[tabs-content-group id="one" active="yes"]' + tinyMCE.activeEditor.selection.getContent() + '[/tabs-content-group]<br />[tabs-content-group id="two"][/tabs-content-group]<br />[/tabs-content]');  }
                },{
                    text: 'Tooltip', 
                    menu: [
                        {
                            text: 'Hover',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[tooltip text="Tooltip Text goes here" trigger="hover"]' + tinyMCE.activeEditor.selection.getContent() + '[/tooltip]');  }
                        },
                        {
                            text: 'Click',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[tooltip text="Tooltip Text goes here" trigger="click"]' + tinyMCE.activeEditor.selection.getContent() + '[/tooltip]');  }
                        },
                    ]
                },{
                    text: 'Progress Bar', 
                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[progress length="50" color="#3bd2f8"]' + tinyMCE.activeEditor.selection.getContent() + '[/progress]');  }
                },{
                    text: 'Popover', 
                    menu: [
                        {
                            text: 'Top',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[popover text="Popover Text goes here" trigger="hover" placement="top"]' + tinyMCE.activeEditor.selection.getContent() + '[/popover]');  }
                        },
                        {
                            text: 'Bottom',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[popover text="Popover Text goes here" trigger="hover" placement="bottom"]' + tinyMCE.activeEditor.selection.getContent() + '[/popover]');  }
                        },
                        {
                            text: 'Left',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[popover text="Popover Text goes here" trigger="hover" placement="left"]' + tinyMCE.activeEditor.selection.getContent() + '[/popover]');  }
                        },
                        {
                            text: 'Right',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[popover text="Popover Text goes here" trigger="hover" placement="right"]' + tinyMCE.activeEditor.selection.getContent() + '[/popover]');  }
                        },
                        {
                            text: 'Hover',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[popover text="Popover Text goes here" trigger="hover"]' + tinyMCE.activeEditor.selection.getContent() + '[/popover]');  }
                        },
                        {
                            text: 'Click',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[popover text="Popover Text goes here" trigger="click"]' + tinyMCE.activeEditor.selection.getContent() + '[/popover]');  }
                        },
                    ]
                },{
                    text: 'Well', 
                    onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[well]' + tinyMCE.activeEditor.selection.getContent() + '[/well]');  }
                },{
                    text: 'Extras', 
                    menu: [
                        {
                            text: 'Intro Text',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[intro-text size="25px"]' + tinyMCE.activeEditor.selection.getContent() + '[/intro-text]');  }
                        },
                        {
                            text: 'Bullet List',
                            onclick : function(){ tinymce.execCommand('mceInsertContent', false, '[bulletlist title="Title of bulletlist" align="left" background="on"]<br />[bulletlist_item]Item 01[/bulletlist_item]<br />[bulletlist_item]Item 02[/bulletlist_item]<br />[/bulletlist]');  }
                        },
                    ]
                },
            ]
        });
    });
    

})();