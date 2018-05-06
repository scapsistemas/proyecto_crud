Ext.onReady(function(){
    Ext.get('mb1').on('click', function(e){
        Ext.MessageBox.show({
           title:'Save Changes?',
           msg: 'You are closing a tab that has unsaved changes. <br />Would you like to save your changes?',
           buttons: Ext.MessageBox.YESNOCANCEL,
           fn: showResult,
           animEl: 'mb4',
           icon: Ext.MessageBox.QUESTION
       });
    });