window.Vue = require('vue');
window.collect = require('collect.js');

import api from './api';
window.api = api;

require("../mixins");

//const files = require.context('../components', true, /\.vue$/i)
//files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));
