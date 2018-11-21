/* 
 * (c) 2018 TRIAL.
 * Created on 16/03/2018, 10:14:01.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

'use strict';

(function (T) {
  
  T.elements.custom(T.elements.TRL_PAGINATION, {
    prototype: Object.create(HTMLDivElement.prototype, {
      createdCallback: {
          value: function () {}
      },
      attributeChangedCallback: {
          value: function () {}
      },
      attachedCallback: {
          value: function () {
            this.setup();
          }
      },
      setData: {
          value: function (page, data) {
            if (data.constructor !== Array)
              throw new TypeError();
            if (typeof page !== 'number' && typeof page !== 'string')
              throw new TypeError();
            if (!this.__data)
              this.__data = [];
            this.__data[page] = data;
          }
      },
      getData: {
          value: function (page) {
            if (!this.__data)
              this.__data = [];
            if (typeof page !== 'number' && typeof page !== 'string')
              return this.__data;
            return this.__data[page] || null;
          }
      },
      clearAllData: {
          value: function () {
            this.__data = [];
          }
      },
      maxItemsCount: {
          set: function (count) {
            if (typeof count !== 'number')
              throw new TypeError();
            if (this.maxItemsCount !== count) {
              this.setAttribute('trl-max-items', count);
              this.setupPagination();
            }
          },
          get: function () {
            return this.hasAttribute('trl-max-items') ? parseInt(this.getAttribute('trl-max-items')) : 0;
          }
      },
      itemsPerPageCount: {
          set: function (count) {
            if (typeof count !== 'number')
              throw new TypeError();
            if (this.itemsPerPageCount !== count) {
              this.setAttribute('trl-items-per-page', count);
              this.setupPagination();
            }
          },
          get: function () {
            return this.hasAttribute('trl-items-per-page') ? parseInt(this.getAttribute('trl-items-per-page')) : 0;
          }
      },
      currentPageOffset: {
          set: function (count) {
            if (typeof count !== 'number')
              throw new TypeError();
            this.setAttribute('trl-current-page', count);
          },
          get: function () {
            return this.hasAttribute('trl-current-page') ? parseInt(this.getAttribute('trl-current-page')) : 0;
          }
      },
      mode: {
          set: function (prefix) {
            if (prefix !== null && typeof prefix !== 'string' && prefix.length <= 0)
              throw new TypeError();
            if (prefix === null)
              this.removeAttribute('trl-mode');
            else 
              this.setAttribute('trl-mode', prefix);
          },
          get: function () {
            return this.hasAttribute('trl-mode') ? this.getAttribute('trl-mode') : null;
          }
      },
      getPage: {
          value: function () {
            if (this.__page === undefined || this.__page === null || !(this.__page instanceof HTMLElement)) {
              this.__page = this.querySelector('#trl-pagination__page');
              if (!(!!this.__page)) {
                this.__page = document.createElement('div');
                this.__page.id = 'trl-pagination__page';
                this.insertBefore(this.__page, this.firstChild);
              }
            }
            return this.__page;
          }
      },
      fetch: {
          value: function () {
            this.loading = false;
            var current_page_offset = (this.mode ? this.mode + '_' : '') + this.currentPageOffset;
            var data = this.getData(current_page_offset);
            if (data !== null) {
              var page = this.getPage();
              while(page.firstChild)
                page.removeChild(page.firstChild);
              var length = data.length < this.itemsPerPageCount ? data.length : this.itemsPerPageCount;
              for (var i = 0; i < length; i++)
                this.dispatchEvent(new CustomEvent('fetchdata', { detail: {
                  page: {
                    offset  : current_page_offset,
                    element : page
                  },
                  item: {
                    offset  : i,
                    value   : data[i]
                  }
                } }));
            }
          }
      },
      onfetchdata: {
          set: function (fn) {
            if (typeof fn !== 'function')
              throw new TypeError('onfetchdata: arg must be a function');
            this.addEventListener('fetchdata', fn);
          }
      },
      onloaddata: {
          set: function (fn) {
            if (typeof fn !== 'function')
              throw new TypeError('loaddata: arg must be a function');
            this.addEventListener('loaddata', fn);
          }
      },
      onpagechanged: {
          set: function (fn) {
            if (typeof fn !== 'function')
              throw new TypeError('pagechanged: arg must be a function');
            this.addEventListener('pagechanged', fn);
          }
      },
      loading: {
          set: function (b) {
            if (typeof b !== 'boolean')
              throw new TypeError('loading: arg must be a boolean');
            this.setAttribute('trl-loading', b);
          },
          get: function () {
            return this.hasAttribute('trl-loading') ? this.getAttribute('trl-loading') === 'true' : false;
          }
      },
      load: {
          value: function () {
            this.loading = true;
            var current_page_offset = (this.mode ? this.mode + '_' : '') + this.currentPageOffset;
            var page = this.getPage();
            var data = this.getData(current_page_offset);
            if (data !== null)
              this.fetch();
            else
              this.dispatchEvent(new CustomEvent('loaddata', { detail: {
                page: {
                  offset  : current_page_offset,
                  element : page
                }
              } }));
          }
      },
      setupPagination: {
        value: function () {
          var pages = this.querySelector('#trl-pagination__btn-pages');
          
          if (!(!!pages)) {
            
            pages = document.createElement('div');
            pages.id = 'trl-pagination__btn-pages';
            this.appendChild(pages);
              
          }
          
          while(pages.firstChild)
            pages.removeChild(pages.firstChild);
            
          var num_pages = this.itemsPerPageCount >= this.maxItemsCount ? 1 : Math.ceil(this.maxItemsCount / this.itemsPerPageCount);
          
          for (var i = 0; i < num_pages; i++) {
            
            var page_btn = document.createElement('button');
            page_btn.id = 'trl-pagination__btn-pages__' + (i + 1);
            page_btn.innerHTML = i + 1;
            
            if (this.currentPageOffset === i)
              page_btn.classList.add('trl-pagination__selected');
            
            page_btn.onclick = function (i, pages, btn) {
              
              if (this.currentPageOffset === i)
                return;
              
              var page = this.getPage();
              
              this.dispatchEvent(new CustomEvent('pagechanged'), { detail: {
                page: {
                  offset  : i,
                  element : page
                }
              } });
            
              try {
                
                pages.getElementsByClassName('trl-pagination__selected')[0]
                        .classList.remove('trl-pagination__selected');
                
              } catch (e) {}
              
              this.currentPageOffset = i;
              
              btn.classList.add('trl-pagination__selected');
              
              this.load();
              
            }.bind(this, i, pages, page_btn);
            
            pages.appendChild(page_btn);
            
          }
        }
      },
      setup: {
        value: function () {
          this.getPage();
          this.setupPagination();
        }
      }
    })
  }, 'Pagination');

  window.dispatchEvent(new CustomEvent('T.elements.Pagination.loaded'));
    
})(window.T);