/**
 * Created by molodyko on 04.08.2016.
 */

/**
 * Client script for async table functionality
 *
 * For using this js you need include it to page as dependency async-table uses jquery
 */
var AsyncTable = (function () {

    /**
     * Init constructor
     * @param parentSelector
     * @param hostUrl
     * @param o
     */
    var constructor = function (parentSelector, hostUrl, o) {
        o = o || {};
        if (!parentSelector || !hostUrl) {
            throw new Error('Parent selector or host url not found');
        }
        this.parentSelector = parentSelector;
        this.url = hostUrl;
        this.filterElementSelector = o.filterElementSelector || '.samsonos-async-table__filter-element';
        this.bodySelector = o.bodySelector || '.samsonos-async-table__body';
        this.paginationSelector = o.paginationSelector || '.samsonos-async-table__pagination';
        this.headerSelector = o.headerSelector || '.samsonos-async-table__header';
        this.sortableButtonSelector = o.sortableButtonSelector || 'a';
        this.paginationButtonSelector = o.paginationButtonSelector || 'a';
        this.waitPrevent = o.waitPrevent || 200;
        this.waitInputChnage = o.waitInputChnage || 600;
        this.loaderClass = o.loaderClass || 'async-table__loader';

        this.useHistory = (o.useHistory !== undefined) ? o.useHistory : true;
        // When browser not support history api
        if (!(window.history && window.history.pushState)) {
            this.useHistory = false;
        }

        // When need find filters(filter class) on the page not only parent block
        this.extendFilters = o.loaderClass !== undefined ? o.loaderClass : true;
    }, self = constructor.prototype;

    /**
     * Plugin id
     *
     * @type {string}
     */
    constructor.PLUGIN_ID = 'samsonos-async-table-plugin';

    /**
     * When table will change self content
     *
     * @type {string}
     */
    constructor.ON_CHANGE = 'samsonos-async-table-on-change';

    /**
     * After table content rendered
     *
     * @type {string}
     */
    constructor.AFTER_RENDER = 'samsonos-async-table-after-render';

    /**
     * Update table
     *
     * @type {string}
     */
    constructor.UPDATE_TABLE = 'samsonos-async-table-update-table';

    /**
     * Init class
     */
    self.init = function () {
        this.bind();
        this.change();

        // On history update
        if (this.useHistory) {
            window.addEventListener("popstate", function(e) {
                // Check state and go to appropriate page
                if (
                    typeof e === 'object' &&
                    typeof e.state === 'object' &&
                    e.state.url && (e.state.id === AsyncTable.PLUGIN_ID)
                ) {
                    this.change(e.state.url);
                }
            }.bind(this));
        }
    };

    /**
     * Bind links
     */
    self.bind = function () {
        // Get filter elements
        var filters = this.extendFilters ? this.e(this.filterElementSelector) : this.find(this.filterElementSelector);

        // Iterate filter and bind links
        filters.each(function (i, elDom) {
            var el = $(elDom), cb = function () {
                setTimeout(function () {
                    this.change();
                }.bind(this), this.waitPrevent);
            }.bind(this);
            if (el.is('select')) {
                el.off('change').change(cb);
            } else if (el.is('input')) {
                if (el.attr('type') === 'checkbox') {
                    el.off('change').change(cb);
                } else if (el.attr('type') === 'text') {
                    el.off('keyup').keyup(function () {
                        setTimeout(function () {
                            this.change();
                        }.bind(this), this.waitInputChnage);
                    }.bind(this));
                }
            }
        }.bind(this));

        // Bind click on sortable and pagination buttons
        this.find(this.headerSelector).find(this.sortableButtonSelector).click(this.handleLink.bind(this));
        this.find(this.paginationSelector).find(this.paginationButtonSelector).click(this.handleLink.bind(this));

        // Add event on update table
        this.e(this.parentSelector).off(AsyncTable.UPDATE_TABLE).on(AsyncTable.UPDATE_TABLE, function () {
            this.change();
        }.bind(this));
    };

    /**
     * Set history
     *
     * @param url
     */
    self.setHistory = function (url) {
        var fullUrl = window.location.origin + window.location.pathname + '?' + url;
        window.history.pushState({url: fullUrl, id: AsyncTable.PLUGIN_ID}, '', fullUrl);
    };

    /**
     * Add loader class
     */
    self.addLoaderClass = function () {
        this.e(this.parentSelector).addClass(this.loaderClass);
    };

    /**
     * Remove loader class
     */
    self.removeLoaderClass = function () {
        this.e(this.parentSelector).removeClass(this.loaderClass);
    };

    /**
     * Handle links
     *
     * @param ev
     */
    self.handleLink = function (ev) {
        ev.preventDefault();
        var el = $(ev.target);
        this.change(el.attr('href'));
    };

    /**
     * On change value
     *
     * @param link
     */
    self.change = function (link) {
        var currentLink = link || window.location.href, data = this.parseParameters(currentLink);

        data.filter = this.getFilterData();
        this.addLoaderClass();

        // Set history
        if  (this.useHistory) {
            this.setHistory($.param(data));
        }

        // Send request to server
        $.ajax({
            type: 'GET',
            url: this.url,
            data: data,
            // Set bundle header for correct work
            beforeSend: function (req) {
                req.setRequestHeader('x-samsonos-async-table-request', 'true');
            }
        }).done(this.update.bind(this)).always(this.removeLoaderClass.bind(this));

        // Trigger custom event
        this.e(this.parentSelector).trigger(AsyncTable.ON_CHANGE, [this, data, link]);
    };

    /**
     * Get filter data
     *
     * @returns {{}}
     */
    self.getFilterData = function () {
        var filter = {};
        // Iterate filter elements and get filter data
        this.find(this.filterElementSelector).each(function (i, elDom) {
            var el = $(elDom), value = el.val();
            if (el.attr('type') === 'checkbox') {
                value = el.prop('checked');
            }
            filter[el.attr('name')] = value;
        }.bind(this));
        return filter;
    };

    /**
     * Update bodies
     *
     * @param response
     */
    self.update = function (response) {
        this.find(this.headerSelector).replaceWith(response.header);
        this.find(this.bodySelector).html(response.body);
        this.find(this.paginationSelector).html(response.pagination);

        // Bind links to new buttons
        this.bind();

        // Trigger custom event
        this.e(this.parentSelector).trigger(AsyncTable.AFTER_RENDER, [this]);
    };

    /**
     * Parse link parameters
     *
     * @param url
     * @returns {{page: number}}
     */
    self.parseParameters = function(url) {
        var getParamsString = url.split('?')[1], parameters = {page: 1};
        if (getParamsString) {
            var getParams = getParamsString.split('&');
            if (getParams) {
                getParams.forEach(function (str) {
                    var params = str.split('=');
                    switch (params[0]) {
                        case 'page':
                            parameters.page = params[1];
                            break;
                        case 'sort':
                            parameters.sort = params[1];
                            break;
                        case 'direction':
                            parameters.direction = params[1];
                            break;
                    }
                });
            }
        }
        return parameters;
    };

    /**
     * Get jquery element by selector
     *
     * @param selector
     * @returns {*|jQuery|HTMLElement}
     */
    self.e = function (selector) {
        return $(selector);
    };

    /**
     * Find element from parent
     *
     * @param selector
     * @returns {*}
     */
    self.find = function (selector) {
        return this.e(this.parentSelector).find(selector);
    };

    return constructor;
}());
