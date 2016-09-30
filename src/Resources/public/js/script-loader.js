/**
 * Created by myslyvyi on 28.09.2016.
 *
 * Load scripts
 */
var ScriptLoader = (function () {

    /**
     * Class constructor
     *
     * @param scripts
     * @param jqueryPath
     * @param successCallback
     * @param catchCallback
     */
    var constructor = function (scripts, jqueryPath, successCallback, catchCallback) {
        if (!scripts) {
            throw new Error('Scripts not found');
        }
        this.jqueryPath = jqueryPath;
        this.scripts = scripts;
        this.successCallback = typeof successCallback === 'function' ? successCallback : function () {};
        this.catchCallback = typeof catchCallback === 'function' ? catchCallback : function () {};
        this.loadedScripts = [];
    }, self = constructor.prototype;

    /**
     * Init class
     */
    self.init = function () {
        // If jquery script was loaded then not load it again because jquery required for plugin work
        if (!window['jQuery']) {
            if (!this.jqueryPath) {
                throw new Error('Jquery path not found');
            }
            this.includeScript('jQuery', this.jqueryPath, this.loadScripts.bind(this));
        } else {
            this.loadScripts();
        }

        return this;
    };

    self.resolveDependency = function () {
        // Scripts for loading current iteration
        var scriptsToLoad = [];
        // Resolve dependency
        this.scripts.forEach(function (scriptObject) {
            // Skip loaders script
            if (this.loadedScripts.indexOf(scriptObject.name) !== -1) {
                return;
            }
            // Add script to iteration if he has not dependencies
            if (!scriptObject.depends) {
                scriptsToLoad.push(scriptObject.name);
            } else { // Add all dependency of this module to load
                scriptObject.depends.forEach(function (scriptName) {
                    if (this.loadedScripts.indexOf(scriptName) !== -1) {
                        scriptsToLoad.push(scriptObject.name);
                    }
                }.bind(this))
            }
        }.bind(this));

        return scriptsToLoad;
    };

    /**
     * Load scripts
     */
    self.loadScripts = function () {
        var scriptPromises = [], scriptsToLoad = this.resolveDependency();
        // Get all scripts loader promises
        scriptsToLoad.forEach(function (scriptName) {
            var script = this.scripts.filter(function (item) {
                return item.name === scriptName;
            });
            if (script[0]) {
                // Get script promise
                scriptPromises.push(this.load(script[0].name, script[0].path));
                this.loadedScripts.push(script[0].name);
            }
        }.bind(this));

        // Can not resolve the dependencies
        if (scriptsToLoad.length === 0) {
            throw new Error('Circular dependencies');
        }

        // Start loading the scripts and call callback
        $.when.apply($, scriptPromises).then(function () {
            // When all scripts were loaded then call final successful callback
            if (this.loadedScripts.length === this.scripts.length) {
                this.successCallback();
            } else { // Call load script next iteration
                this.loadScripts();
            }
        }.bind(this), this.catchCallback);
    };

    /**
     * Add successful handler
     *
     * @param cb
     */
    self.then = function (cb) {
       this.successCallback = cb;

        return this;
    };

    /**
     * Add error handler
     *
     * @param cb
     */
    self.catch = function (cb) {
        this.catchCallback = cb;

        return this;
    };

    /**
     * Load script
     *
     * @param name
     * @param path
     * @returns {*}
     */
    self.load = function (name, path) {
        var promise = $.Deferred();
        this.includeScript(name, path, function () {
            // Resolve script loading
            promise.resolve(name);
        });
        return promise;
    };

    /**
     * Include script on page
     *
     * @param name
     * @param path
     * @param callback
     */
    self.includeScript = function(name, path, callback) {
        if (!window[name]) {
            var script = document.createElement("script");
            script.type = "text/javascript";
            if (script.readyState) {  //IE
                script.onreadystatechange = function () {
                    if (script.readyState === "loaded" || script.readyState === "complete") {
                        script.onreadystatechange = null;
                        if (callback) {
                            callback();
                        }
                    }
                };
            } else {  //Others
                script.onload = function () {
                    if (callback) {
                        callback();
                    }
                };
            }
            script.src = path;
            document.getElementsByTagName("head")[0].appendChild(script);
        } else {
            callback();
        }
    };

    return constructor;
}());
