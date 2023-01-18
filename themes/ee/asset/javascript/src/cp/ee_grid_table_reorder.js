$.fn.eeGridTableReorder = function(params) {
	return this.each(function() {
		var that = this,
			defaults = {
				sortableContainer: 'tbody',
				handle: 'td.reorder-col',
				cancel: 'td.sort-cancel',
				item: '> tr',
				containment: 'parent',
			},
			config = {};

		config = $.extend(config, defaults, params);

		$(config.sortableContainer, this).sortable({
			axis: 'y',
			forcePlaceholderSize: true,
			appendTo: config.appendTo,
			handle: config.handle,
			cancel: config.cancel,
			items: config.item,
			containment: config.containment,
			sort: EE.sortable_sort_helper,
			cursor: "move",
			tolerance: 'pointer',
			sort: (event, ui) => {
				try {
					EE.sortable_sort_helper(event, ui)
				} catch (error) {}
			},
			helper: function(event, row)	// Fix issue where cell widths collapse on drag
			{
				var $originals = row.children();
				var $helper = row.clone();

				$helper.find('input[type=radio]:enabled').each(function() {
					$(this).attr('name', Math.random() * 20);
				});

				$helper.children().each(function(index)
				{
					// Set helper cell sizes to match the original sizes
					$(this).width($originals.eq(index).outerWidth())
				});

				return $helper;
			},
			// Before sort starts
			start: function(event, ui)
			{
				if (params.beforeSort !== undefined)
				{
					params.beforeSort(ui.item);
				}
			},
			// After sort finishes
			stop: function(event, ui)
			{
				if (params.afterSort !== undefined)
				{
					params.afterSort(ui.item);
				}
			}
		});
	});
}