$(function () {
  'use strict';


  var colors = {
    primary: "#6571ff",
    secondary: "#7987a1",
    success: "#05a34a",
    info: "#66d1d1",
    warning: "#fbbc06",
    danger: "#ff3366",
    light: "#e9ecef",
    dark: "#060c17",
    muted: "#7987a1",
    gridBorder: "rgba(77, 138, 240, .15)",
    bodyColor: "#b8c3d9",
    cardBg: "#0c1427"
  }

  var fontFamily = "'iransans', Helvetica, sans-serif"



  // Line Chart
  new Morris.Line({
    element: 'morrisLine',
    data: [{
        year: '1396',
        value: 2
      },
      {
        year: '1397',
        value: 9
      },
      {
        year: '1398',
        value: 5
      },
      {
        year: '1399',
        value: 12
      },
      {
        year: '1400',
        value: 5
      }
    ],
    xkey: 'year',
    ykeys: ['value'],
    labels: ['مقدار'],
    lineColors: [colors.danger],
    gridLineColor: [colors.gridBorder],
    gridTextColor: colors.bodyColor,
    gridTextFamily: fontFamily,
  });




  // Area Chart
  Morris.Area({
    element: 'morrisArea',
    data: [{
        y: '1394',
        a: 100,
        b: 90
      },
      {
        y: '1395',
        a: 75,
        b: 65
      },
      {
        y: '1396',
        a: 50,
        b: 40
      },
      {
        y: '1397',
        a: 75,
        b: 65
      },
      {
        y: '1398',
        a: 50,
        b: 40
      },
      {
        y: '1399',
        a: 75,
        b: 65
      },
      {
        y: '1400',
        a: 100,
        b: 90
      }
    ],
    xkey: 'y',
    ykeys: ['a', 'b'],
    labels: ['سری الف', 'سری ب'],
    lineColors: [colors.danger, colors.info],
    fillOpacity: 0.1,
    gridLineColor: [colors.gridBorder],
    gridTextColor: colors.bodyColor,
    gridTextFamily: fontFamily,
  });



  // Bar Chart
  Morris.Bar({
    element: 'morrisBar',
    data: [{
        y: '1394',
        a: 100,
        b: 90
      },
      {
        y: '1395',
        a: 75,
        b: 65
      },
      {
        y: '1396',
        a: 50,
        b: 40
      },
      {
        y: '1397',
        a: 75,
        b: 65
      },
      {
        y: '1398',
        a: 50,
        b: 40
      },
      {
        y: '1399',
        a: 75,
        b: 65
      },
      {
        y: '1400',
        a: 100,
        b: 90
      }
    ],
    xkey: 'y',
    ykeys: ['a', 'b'],
    labels: ['سری الف', 'سری ب'],
    barColors: [colors.danger, colors.info],
    gridLineColor: [colors.gridBorder],
    gridTextColor: colors.bodyColor,
    gridTextFamily: fontFamily,
  });



  // Donut Chart
  Morris.Donut({
    element: 'morrisDonut',
    data: [{
        label: "فروش دانلودی",
        value: 12
      },
      {
        label: "فروش در فروشگاه",
        value: 30
      },
      {
        label: "فروش با سفارش ایمیلی",
        value: 20
      }
    ],
    colors: [colors.danger, colors.info, colors.primary],
    labelColor: colors.bodyColor,
  });

});