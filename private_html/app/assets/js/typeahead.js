$(function () {
  'use strict'

  var substringMatcher = function (strs) {
    return function findMatches(q, cb) {
      var matches, substringRegex;

      // an array that will be populated with substring matches
      matches = [];

      // regex used to determine if a string contains the substring `q`
      var substrRegex = new RegExp(q, 'i');

      // iterate through the pool of strings and for any string that
      // contains the substring `q`, add it to the `matches` array
      for (var i = 0; i < strs.length; i++) {
        if (substrRegex.test(strs[i])) {
          matches.push(strs[i]);
        }
      }

      cb(matches);
    };
  };

  var states = ['مرکزی', 'اردبیل', 'آذربایجان غربی', 'اصفهان', 'خوزستان',
    'ایلام', 'خراسان شمالی', 'هرمزگان', 'بوشهر', 'خراسان جنوبی', 'آذربایجان شرقی',
    'تهران', 'لرستان', 'گیلان', 'سیستان و بلوچستان', 'زنجان', 'مازندران', 'سمنان',
    'کردستان', 'چهارمحال و بختیاری', 'فارس', 'قزوین', 'قم',
    'البرز', 'کرمان', 'کرمانشاه', 'گلستان', 'خراسان رضوی', 'همدان',
    'چهارمحال و بختیاری', 'کهکیلویه و بویراحمد', 'یزد'
  ];

  $('#the-basics .typeahead').typeahead({
    hint: true,
    highlight: true,
    minLength: 1
  }, {
    name: 'states',
    source: substringMatcher(states)
  });
  // constructs the suggestion engine
  var states = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.whitespace,
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    // `states` is an array of state names defined in "The Basics"
    local: states
  });

  $('#bloodhound .typeahead').typeahead({
    hint: true,
    highlight: true,
    minLength: 1
  }, {
    name: 'states',
    source: states
  });
});