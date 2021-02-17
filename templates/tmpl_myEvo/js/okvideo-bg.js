$(function(){
$.okvideo({ source: '-JSO5X1TgqY',
          volume: 0, 
          loop: true,
          hd:true, 
          adproof: true,
          annotations: false,
          onFinished: function() { console.log('finished') },
          unstarted: function() { console.log('unstarted') },
          onReady: function() { console.log('onready') },
          onPlay: function() { console.log('onplay') },
          onPause: function() { console.log('pause') },
          buffering: function() { console.log('buffering') },
          cued: function() { console.log('cued') },
       });
});
//  nice video -JSO5X1TgqY -0VmXQA-RaU v9CNgN33DRE 88534335

