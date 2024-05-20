(function() {
  var callWithJQuery;

  callWithJQuery = function(pivotModule) {
    if (typeof exports === "object" && typeof module === "object") {
      return pivotModule(require("jquery"));
    } else if (typeof define === "function" && define.amd) {
      return define(["jquery"], pivotModule);
    } else {
      return pivotModule(jQuery);
    }
  };

  callWithJQuery(function($) {
    var c3r, d3r, frFmt, frFmtInt, frFmtPct, gcr, nf, r, tpl;
    nf = $.pivotUtilities.numberFormat;
    tpl = $.pivotUtilities.aggregatorTemplates;
    r = $.pivotUtilities.renderers;
    gcr = $.pivotUtilities.gchart_renderers;
    d3r = $.pivotUtilities.d3_renderers;
    c3r = $.pivotUtilities.c3_renderers;
    frFmt = nf({
      thousandsSep: ",",
      decimalSep: "."
    });
    frFmtInt = nf({
      digitsAfterDecimal: 0,
      thousandsSep: ",",
      decimalSep: "."
    });
    frFmtPct = nf({
      digitsAfterDecimal: 2,
      scaler: 100,
      suffix: "%",
      thousandsSep: ",",
      decimalSep: "."
    });
    $.pivotUtilities.locales.zh = {
      localeStrings: {
        renderError: "顯示結果時發生錯誤。",
        computeError: "計算結果時發生錯誤。 ",
        uiRenderError: "顯示介面時發生錯誤。",
        selectAll: "選擇全部",
        selectNone: "全部不選",
        tooMany: "(因資料過多而無法上市)",
        filterResults: "",
        totals: "合計",
        vs: "於",
        by: "分組於"
      },
      aggregators: {
        "計數": tpl.count(frFmtInt),
        "非重複值的個數": tpl.countUnique(frFmtInt),
        "列出非重複值": tpl.listUnique(", "),
        "求和": tpl.sum(frFmt),
        "求和後取整": tpl.sum(frFmtInt),
        "平均值": tpl.average(frFmt),
        "中位数": tpl.median(frFmt),
        "方差": tpl["var"](1, frFmt),
        "樣本標準差": tpl.stdev(1, frFmt),
        "最小值": tpl.min(frFmt),
        "最大值": tpl.max(frFmt),
        "第一": tpl.first(frFmt),
        "最後": tpl.last(frFmt),
        "兩和之比": tpl.sumOverSum(frFmt),
        "二分配：置信度為80 %時的區間上限": tpl.sumOverSumBound80(true, frFmt),
        "二項分佈：置信度為80%時的區間下限": tpl.sumOverSumBound80(false, frFmt),
        "和在總計中的比例": tpl.fractionOf(tpl.sum(), "total", frFmtPct),
        "和在行合計中的比例": tpl.fractionOf(tpl.sum(), "row", frFmtPct),
        "和在列合計中的比例": tpl.fractionOf(tpl.sum(), "col", frFmtPct),
        "計數在總計中的比例": tpl.fractionOf(tpl.count(), "total", frFmtPct),
        "計數在行合計中的比例": tpl.fractionOf(tpl.count(), "row", frFmtPct),
        "計數在列合計中的比例": tpl.fractionOf(tpl.count(), "col", frFmtPct)
      },
      renderers: {
        "表格": r["Table"],
        "表格内柱狀圖": r["Table Barchart"],
        "熱圖": r["Heatmap"],
        "行熱圖": r["Row Heatmap"],
        "列熱圖": r["Col Heatmap"]
      }
    };
    if (gcr) {
      $.pivotUtilities.locales.zh.gchart_renderers = {
        "折線圖(g)": gcr["Line Chart"],
        "長條圖(g)": gcr["Bar Chart"],
        "堆疊長條圖(g)": gcr["Stacked Bar Chart"],
        "面積圖(g)": gcr["Area Chart"]
      };
      $.pivotUtilities.locales.zh.renderers = $.extend($.pivotUtilities.locales.zh.renderers, $.pivotUtilities.locales.zh.gchart_renderers);
    }
    if (d3r) {
      $.pivotUtilities.locales.zh.d3_renderers = {
        "樹圖": d3r["Treemap"]
      };
      $.pivotUtilities.locales.zh.renderers = $.extend($.pivotUtilities.locales.zh.renderers, $.pivotUtilities.locales.zh.d3_renderers);
    }
    if (c3r) {
      $.pivotUtilities.locales.zh.c3_renderers = {
        "折線圖": c3r["Line Chart"],
        "長條圖": c3r["Bar Chart"],
        "堆疊長條圖": c3r["Stacked Bar Chart"],
        "面積圖": c3r["Area Chart"],
        "散佈圖": c3r["Scatter Chart"]
      };
      $.pivotUtilities.locales.zh.renderers = $.extend($.pivotUtilities.locales.zh.renderers, $.pivotUtilities.locales.zh.c3_renderers);
    }
    return $.pivotUtilities.locales.zh;
  });

}).call(this);

//# sourceMappingURL=pivot.zh.js.map
