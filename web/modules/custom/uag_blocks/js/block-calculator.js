(function ($, Drupal) {
  Drupal.behaviors.calculator_block = {
    attach: function (context, settings) {
      $('.calculator-block', context).once('calculator_block').each(function () {
        // Apply the myCustomBehaviour effect to the elements only once.
        $(this).addClass('js-calculator-block');

        const types = settings.calculator_block.storage_types;
        const table_element = $('table.storage', this);
        const table_summary = $('ul.summary', this);
        const graph_element = $('.graph', this)[0];
        const boiler_power_element = $(".boiler_output", this);

        let boiler_power = boiler_power_element.val();

        let storage = [
          new Storage('lpg', types.lpg.ttw, types.lpg.kpd, Drupal.t('LPG')),
          new Storage('gaz', types.gaz.ttw, types.gaz.kpd, Drupal.t('Natural gas')),
          new Storage('diesel', types.diesel.ttw, types.diesel.kpd, Drupal.t('Diesel')),
          new Storage('electricity', types.electricity.ttw, types.electricity.kpd, Drupal.t('Electricity')),
          new Storage('pellets', types.pellets.ttw, types.pellets.kpd, Drupal.t('Pellets'))
        ];

        let graph = initGraph(storage, graph_element, boiler_power);

        boiler_power_element.keyup(function() {
          if (boiler_power_element.val() < 1 ){
            boiler_power_element.val(1);
          }
          boiler_power_element.keypress(validateNumber);
          boiler_power = boiler_power_element.val();

          updateTable(storage, table_element, boiler_power);
          updateTableSummary(storage, table_summary, boiler_power);
          updateGraph(graph, storage, boiler_power);
        });

        $('.columns>.col', this).each(function (index) {
          const col = $(this);
          storage[index].set_price($('.price input', col).val());

          $('.slider', col).slider({
            range: "min",
            value: storage[index].price,
            min: 0,
            max: 50,
            step: 0.01,
            slide: function(event, ui) {
              $('.price input', col).val(ui.value);
              storage[index].set_price(ui.value);

              updateTable(storage, table_element, boiler_power);
              updateTableSummary(storage, table_summary, boiler_power);
              updateGraph(graph, storage, boiler_power);
            }
          })
        });

        updateTable(storage, table_element, boiler_power);
        updateTableSummary(storage, table_summary, boiler_power);
        updateGraph(graph, storage, boiler_power);
      });
    }
  };

  const kkal2kW = 860;

  function Storage(name, tt_w, kpd, title) {
    this.name = name;
    this.tt_w = tt_w;
    this.kpd = kpd;
    this.price = 0;
    this.title = title;

    this.get_tt_kkal = function () {
      return this.tt_w * kkal2kW
    };
    
    this.get_hourly = function (boiler_output) {
      return boiler_output / (this.tt_w * this.kpd);
    };

    this.get_daily = function (boiler_output) {
      return this.get_hourly(boiler_output) * 0.7 * 12;
    };

    this.get_monthly = function (boiler_output) {
      return this.get_daily(boiler_output) * 30;
    };

    this.get_yearly =function (boiler_output) {
      return this.get_daily(boiler_output) * 178;
    };

    this.set_tt_w = function (ttw) {
      this.tt_w = ttw;
      return this;
    };

    this.set_kpd = function (kpd) {
      this.kpd = kpd;
      return this;
    };

    this.set_price = function (price) {
      this.price = price;
      return this;
    }
  }

  function updateTable(storage, tableElement, boiler_power) {
    storage.forEach(function (item, i) {
      let ttw_element = $('tbody tr', tableElement).eq(0).find('td').eq(i+2);
      let ttk_element = $('tbody tr', tableElement).eq(1).find('td').eq(i+2);
      let kpd_element = $('tbody tr', tableElement).eq(2).find('td').eq(i+2);
      let hour_element = $('tbody tr', tableElement).eq(3).find('td').eq(i+2);
      let day_element = $('tbody tr', tableElement).eq(4).find('td').eq(i+2);
      let month_element = $('tbody tr', tableElement).eq(5).find('td').eq(i+2);
      let year_element = $('tbody tr', tableElement).eq(6).find('td').eq(i+2);

      ttw_element.text(item.tt_w.toFixed(2));
      ttk_element.text(item.get_tt_kkal().toFixed(2));
      kpd_element.text(item.kpd.toFixed(2));
      hour_element.text(item.get_hourly(boiler_power).toFixed(2));
      day_element.text(item.get_daily(boiler_power).toFixed(2));
      month_element.text(item.get_monthly(boiler_power).toFixed(2));
      year_element.text(item.get_yearly(boiler_power).toFixed(2));
    })
  }

  function updateTableSummary(storage, tableElement, boiler_power) {
    storage.forEach(function (item, i) {
      $('li', tableElement).eq(i).text((item.get_yearly(boiler_power) * item.price).toFixed(2));
    });
  }

  function validateNumber(event) {
    const key = window.event ? event.keyCode : event.which;
    if (event.keyCode === 8 || event.keyCode === 46) {
      return true;
    } else return !(key < 48 || key > 57);
  }

  function initGraph(storage, graphElement, boiler_power) {
    var chart = new Chart(graphElement.getContext('2d'), {
      type: 'bar',
      data: {
        labels: storage.map(function (storage_item) {
          return storage_item.title;
        }),
        datasets: [{
          // backgroundColor: 'rgb(255, 99, 132)',
          // borderColor: 'rgb(255, 99, 132)',
          data: storage.map(function (storage_item) {
            let value =(storage_item.get_yearly(boiler_power) * storage_item.price).toFixed(2);
            return value;
          }),
          backgroundColor: [
            '#326896',
            '#3DC2C1',
            '#FCA428',
            '#FA0D1B',
            '#000000',
          ],
          /*
          borderColor: [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
          ],
          borderWidth: 1*/
        }],
      },
      options: {
        legend: {
          display: false,
        },
        tooltips: {
          enabled: false
        },
        title: {
          display: true,
          text: Drupal.t('Графік цін на топливо в рік'),
        }
      }
    });

    return chart;
  }

  function updateGraph(graph, storage, boiler_power) {

    // console.log(graph);
    graph.data.datasets[0].data = storage.map(function (storage_item) {
      return (storage_item.get_yearly(boiler_power) * storage_item.price).toFixed(2);
    });
    graph.update();
  }
})(jQuery, Drupal);
