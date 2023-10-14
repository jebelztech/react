var config = {
    /*paths: {
        customjs:               'js/custom'
    } */
    paths: {            
         'myfile': "js/custom"
      },   
    shim: {
        'myfile': {
            deps: ['jquery']
        }
    }
};