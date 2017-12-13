/**
 * Magegiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the magegiant.com license that is
 * available through the world-wide-web at this URL:
 * http://magegiant.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magegiant
 * @package     Magegiant_GiftCard
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement/
 */
var GifCardTemplate = GifCardTemplate || {};

(function ($) {
    // USE STRICT
    "use strict";
    GifCardTemplate.popup = {
        init: function (config) {
            this.overlayEl = $(config.overlayId);
            this.popupContainer = $(config.popupContainer);
            this.showPopupBtn = $(config.showPopupBtn);
            this.hidePopupBtn = $(config.hidePopupBtn);
            this.initPopupUrl = config.initPopupUrl;
            this.initObservers();
        },
        initObservers: function () {
            this.showPopupBtn.click(function () {
                this.showPopup();
                //this.initItems();
            }.bind(this));
            this.hidePopupBtn.click(function () {
                this.hidePopup();
            }.bind(this));
            this.overlayEl.click(function () {
                this.hidePopup();
            }.bind(this))
        },
        alignCenter: function () {
            var windowWidth = document.documentElement.clientWidth;
            var windowHeight = document.documentElement.clientHeight;
            var popupHeight = this.popupContainer.height();
            var popupWidth = this.popupContainer.width();
            //centering
            this.popupContainer.css({
                "position": "absolute",
                "top": Math.abs(windowHeight / 2 - popupHeight / 2),
                "left": Math.abs(windowWidth / 2 - popupWidth / 2)
            });
            $('body').scrollTop(Math.abs(windowHeight / 2 - popupHeight / 2) - 50)
            //only need force for IE6
            this.overlayEl.css({
                "height": windowHeight
            });
        },
        showPopup: function () {
            /*Fix bug 1.9*/
            $('.main-container').css({
                position: 'initial'
            })
            this.alignCenter();
            this.overlayEl.addClass('overlay');
            this.popupContainer.fadeIn('slow');
        },
        hidePopup: function () {
            this.popupContainer.fadeOut('slow');
            this.overlayEl.removeClass('overlay');
            this.overlayEl.css({
                "height": 0
            })
        }
    };
    GifCardTemplate.processor = {
        initialize: function (config) {
            this.init();
            this.setBlocks(config.blocks);
            this.blockMapping = config.blockMapping;
            this.loadingClass = config.loadingClass;
            this.actionPattern = config.actionPattern;
        },
        init: function () {
            this.blocks = {};
            this.queueBlocks = {};
            this.currentRequest = '';
            this.queueRequest = [];
        },
        setBlocks: function (blocks) {
            for (var index in blocks) {
                this._addBlock(index, blocks[index]);
            }
        },
        _addBlock: function (name, selector) {
            if (typeof(this.blocks[name]) != 'undefined') {
                return;
            }
            this.blocks[name] = $(selector);
        },
        newRequest: function (url, options) {
            var action = this._getActionFromUrl(url, this.actionPattern);
            var options = options || {};
            var requestOptions = Object.extend({}, options);
            var self = this;
            requestOptions = Object.extend(requestOptions, {
                onComplete: function (transport) {
                    self.onComplete(transport, action);
                    if (options.onComplete) {
                        options.onComplete(transport);
                    }
                }
            });
            this.currentRequest = new Ajax.Request(url, requestOptions);
        },
        Request: function (url, options) {
            var action = this._getActionFromUrl(url, this.actionPattern);
            this.addBlocksToQueue(action);
            if (this.currentRequest === '') {
                this.newRequest(url, options);
            } else {
                this.queueRequest.push([url, options]);
            }
        },
        reRequest: function (url, options) {
            this.newRequest(url, options);
        },
        onComplete: function (transport, action) {
            try {
                var response = JSON.parse(transport.responseText);
            } catch (e) {
                var response = {
                    blocks: {}
                };
            }
            this.removeBlockQueue(action, response);
            this.currentRequest = '';
            if (this.queueRequest.length > 0) {
                this._emptyQueue();
                var args = this.queueRequest.shift();
                this.reRequest(args[0], args[1]);
            }
        },
        removeBlockQueue: function (action, response) {
            if (!action || !this.blockMapping[action]) {
                return;
            }
            var response = response || {};
            var responseBlocks = response.blocks || {};
            this.blockMapping[action].each(function (blockName) {
                if (!this.blocks[blockName]) {
                    return;
                }
                this.queueBlocks[blockName]--;
                if (this.queueBlocks[blockName] === 0) {
                    if (responseBlocks[blockName]) {
                        this.blocks[blockName].html(responseBlocks[blockName]);
                    }
                    if ("removeQueueBeforeFinish" in this.blocks[blockName]) {
                        this.blocks[blockName].removeQueueBeforeFinish(response);
                    }
                    this.removeLoading(this.blocks[blockName]);
                    if ("removeQueueAfterFinish" in this.blocks[blockName]) {
                        this.blocks[blockName].removeQueueAfterFinish(response);
                    }
                }
            }.bind(this));
        },
        removeLoading: function (block, loadingClass) {
            var className = loadingClass;
            if (!className) {
                className = this.loadingClass;
            }
            var selector = "." + className;
            block.css({
                'position': ''
            })
            block.children(selector).each(function () {
                this.remove();
            });
        },
        _getActionFromUrl: function (url, pattern) {
            var matches = url.match(pattern);
            if (!matches || !matches[1]) {
                return null;
            }
            return matches[1];
        },
        addBlocksToQueue: function (action) {
            if (!action || !this.blockMapping[action]) {
                return;
            }
            this.blockMapping[action].each(function (name) {
                if (typeof(this.queueBlocks[name]) === 'undefined') {
                    this.queueBlocks[name] = 0;
                }
                if (!this.blocks[name]) {
                    return;
                }
                if (this.queueBlocks[name] === 0) {
                    var selection = this.blocks[name];
                    if ("appendQueueBeforeFinish" in this.blocks[name]) {
                        this.blocks[name].appendQueueBeforeFinish();
                    }
                    this.appendLoading(selection);
                    if ("appendQueueAfterFinish" in this.blocks[name]) {
                        this.blocks[name].appendQueueAfterFinish();
                    }
                }
                this.queueBlocks[name]++;
            }.bind(this));
        },
        _emptyQueue: function () {
            var actions = [];
            var removed = [];
            //Fix for muli click to element when processing
            this.queueRequest.reverse().each(function (args, key) {
                var action = this._getActionFromUrl(args[0], this.actionPattern);
                if (actions.indexOf(action) === -1) {
                    actions.push(action);
                } else {
                    removed.push(key);
                }
            }.bind(this));
            var newQueue = [];
            this.queueRequest.each(function (args, key) {
                var action = this._getActionFromUrl(args[0], this.actionPattern);
                if (removed.indexOf(key) === -1) {
                    newQueue.push(args);
                } else {
                    this.removeBlockQueue(action);
                }
            }.bind(this));
            this.queueRequest = newQueue.reverse();
        },
        appendLoading: function (block, loadingClass) {
            var className = loadingClass;
            if (!className) {
                className = this.loadingClass;
            }
            block.css({
                'position': 'relative'
            })
            var ajaxLoading = new Element('div');
            ajaxLoading.addClassName(className);
            var child = block.children().first();
            child.before(ajaxLoading);
        }

    };
    GifCardTemplate.format = {
        initialize: function (config) {
            this.changeFormatUrl = config.changeFormatUrl;
            this.formatEls = $(config.formatEls);
            this.currentFormat = $(config.currentFormat);
            this.initObservers();
        },
        initObservers: function () {
            var me = this;
            this.formatEls.click(function () {
                GifCardTemplate.design.items.reloadItemContent();
                var el = this;
                me.currentFormat.val(el.value);
                var currentDesign = GifCardTemplate.design.currentDesign;
                var requestOptions = {
                    parameters: {
                        format_type: el.value,
                        design_type: currentDesign ? currentDesign.val() : '',
                        mode_type: GifCardTemplate.design.items.currentMode.val()
                    }
                };
                me.changeFormat(requestOptions)

            })
        },
        changeFormat: function (requestOptions) {
            GifCardTemplate.processor.Request(this.changeFormatUrl, requestOptions);

        }
    };
    GifCardTemplate.design = {
        initialize: function (config) {
            this.changeDesignUrl = config.changeDesignUrl;
            this.designEls = $(config.designEls);
            this.currentDesign = $(config.currentDesign);
            this.initObservers();
        },
        initObservers: function () {
            var me = this;
            var format = GifCardTemplate.format.currentFormat;
            this.designEls.click(function () {
                GifCardTemplate.design.items.reloadItemContent();
                var el = this;
                me.currentDesign.val(el.value);
                var requestOptions = {
                    parameters: {
                        design_type: el.value,
                        format_type: format ? format.val() : '',
                        mode_type: GifCardTemplate.design.items.currentMode.val()
                    }
                };
                me.changeDesign(requestOptions)

            })
        },
        changeDesign: function (requestOptions) {
            GifCardTemplate.processor.Request(this.changeDesignUrl, requestOptions);
        }
    };
    GifCardTemplate.design.items = {
        initialize: function (config) {
            this.changeModeUrl = config.changeModeUrl;
            this.itemWrapper = $(config.itemWrapper);
            this.itemUploadWrapper = $(config.itemUploadWrapper);
            this.modeEls = $(config.modeEls);
            this.currentMode = $(config.currentMode);
            this.itemEls = $(config.itemEls);
            this.videoEls = $(config.videoEls);
            this.useSelectedItem = $(config.useSelectedItem);
            this.hasImgUploaded = false;
            this.initObservers();
        },
        reloadItemContent: function () {
            this.itemWrapper.show();
            this.itemUploadWrapper.hide();
        },
        initObservers: function () {
            var me = this;
            var format = GifCardTemplate.format.currentFormat;
            this.modeEls.click(function () {
                var el = $(this);
                var mode_type;
                if (el.hasClass('fa-th')) {
                    mode_type = 'grid';
                }
                else {
                    mode_type = 'list';
                }
                me.currentMode.val(mode_type);
                var requestOptions = {
                    parameters: {
                        mode_type: mode_type,
                        format_type: format ? format.val() : '',
                        design_type: GifCardTemplate.design.currentDesign.val()
                    }
                };
                me.activeMode(el);
                me.changeMode(requestOptions)

            });
            this.itemEls.click(function () {
                var el = $(this);
                me.selectedItem = el.parent();
                me.activeItem(el);
                me.checkUploadFormat();

            });
            this.videoEls.click(function () {
                var el = $(this);
                me.selectedItem = el.parent();
                me.activeItem(el);
                me.checkUploadFormat();

            });
            this.itemEls.dblclick(function () {
                var el = $(this);
                me.selectedItem = el.parent();
                me.activeItem(el);
                if (me.checkUploadFormat()) {
                    me.showUploadArea();
                }
                else {
                    me.updateProductImg();
                }
            });
            this.useSelectedItem.click(function () {
                if (me.checkUploadFormat()) {
                    me.showUploadArea();
                }
                else {
                    me.updateProductImg();
                }
            });
        },
        updateProductImg: function () {
            if (GifCardTemplate.design.items.hasImgUploaded) {
                var designed = GifCardTemplate.design.upload.templateContent.clone();
            }
            else {
                var designed = this.selectedItem.clone();
            }
            var img = designed.find('img');
            if (img.hasClass('video-thumb')) {
                img.hide();
                designed.find('iframe').show();
            }
            if (img.attr('source')) {
                img.attr('src', img.attr('source'));
            }
            GifCardTemplate.form.updateProductHtml(designed);
            var customUpload = GifCardTemplate.form.productImgEl.find('.custom-upload');
            if (customUpload.length) {
                GifCardTemplate.design.upload.resizeCustomUpload(img, customUpload, true);
            }
            GifCardTemplate.popup.hidePopup();
        },
        checkUploadFormat: function () {
            var item = this.selectedItem.find('img');
            if (item.hasClass('upload') && !this.hasImgUploaded) {
                return true;
            }
            return false;
        },
        showUploadArea: function () {
            this.itemWrapper.hide();
            this.itemUploadWrapper.show();
            var selectedImg = this.selectedItem.find('img');
            var upload = GifCardTemplate.design.upload;
            upload.templateContent[0].id = this.selectedItem[0].id;
            upload.uploadFrameEl.attr('src', selectedImg.attr('src'));
            upload.resizeCustomUpload();
            if (upload.uploadedImg) {
                this.hasImgUploaded = true;
            }

        },
        activeItem: function (item) {

            if (!this.useSelectedItem.children().hasClass('active')) {
                this.useSelectedItem.children().addClass('active');
                this.useSelectedItem.prop('disabled', false);
            }
            if (item) {
                this.itemEls.each(function () {
                    var el = $(this);
                    if (item.is(el)) {
                        el.addClass('active');
                    }
                    else {
                        el.removeClass('active');
                    }
                });
                this.videoEls.each(function () {
                    var el = $(this);
                    if (item.is(el)) {
                        el.addClass('active');
                    }
                    else {
                        el.removeClass('active');
                    }
                });
            }
        },
        activeMode: function (mode) {
            this.modeEls.each(function () {
                var el = $(this);
                if (el.is(mode)) {
                    el.addClass('active');
                }
                else {
                    el.removeClass('active');
                }
            })
        },
        changeMode: function (requestOptions) {
            GifCardTemplate.processor.Request(this.changeModeUrl, requestOptions);
        }
    };
    GifCardTemplate.design.upload = {
        initialize: function (config) {
            this.templateContent = $(config.templateContent);
            this.uploadFrameEl = $(config.uploadFrameEl);
            this.uploadContent = $(config.uploadContent);
            this.uploadArea = $(config.uploadArea);
            this.uploadLabel = config.uploadLabel;
            this.uploadAnotherLabel = config.uploadAnotherLabel;
            this.uploadedImg = '';
            this.angle = 0;
            this.backDesignEl = $(config.backDesignEl);
            this.uploadAnotherEl = $(config.uploadAnotherEl);
            this.cropResizeArea = $(config.cropResizeArea);
            this.rotateBtn = $(config.rotateBtn);
            this.zoomInBtn = $(config.zoomInBtn);
            this.zoomOutBtn = $(config.zoomOutBtn);
            this.allowType = config.allowType;
            this.uploadUrl = config.uploadUrl;
            this.baseImageUrl = config.baseImageUrl;
            this.maxSize = config.maxSize;
            this.imgPosition = {
                top: 0,
                left: 0
            };
            this.initUpload();
            this.initObservers();
        },
        resizeCustomUpload: function (el, customUpload, isProduct) {
            if (!el) {
                var el = this.uploadFrameEl;
            }
            var widthRatio = el.width() / 585;
            var heightRatio = el.height() / 302;
            var uploadWidth = widthRatio * 279;
            var uploadHeight = heightRatio * 179;
            var uploadTop = heightRatio * 62;
            var uploadLeft = widthRatio * 158;
            if (!customUpload) {
                var customUpload = this.uploadContent;
            }
            $(customUpload).css({
                width: Math.ceil(uploadWidth) + 'px',
                height: uploadHeight + 'px',
                top: uploadTop + 'px',
                left: uploadLeft + 'px'
            })
            if (isProduct) {
                var imgRatio = this.uploadedImg.width() / this.uploadContent.width();
                var img = customUpload.find('img');
                img.css({
                    width: customUpload.width() * imgRatio.toFixed(1) + 'px',
                    maxWidth: 'none',
                    maxHeight: 'none',
                    margin: '0'
                })
            }
            var dropZone = customUpload.find('.dropper-dropzone');
            if (dropZone) {
                dropZone.css({
                    width: customUpload.width() + 'px',
                    height: customUpload.height() + 'px'
                })
            }
        },
        initUpload: function () {
            var me = this;
            this.uploadArea.dropper({
                action: this.uploadUrl,
                maxSize: this.maxSize,
                label: this.uploadLabel,
                multiple: false
            })
                .on("start.dropper", me.onStart.bind(this))
                .on("fileComplete.dropper", me.onFileComplete.bind(this))
            this.uploadAnotherEl.dropper({
                action: this.uploadUrl,
                maxSize: this.maxSize,
                label: this.uploadAnotherLabel,
                multiple: false
            })
                .on("start.dropper", me.onStart.bind(this))
                .on("fileComplete.dropper", me.onFileComplete.bind(this))

        },
        addAjaxLoading: function (target, loadingClass) {
            var loading = $('<div>', {
                class: loadingClass
            })
            target.append(loading);
        },
        removeAjaxLoading: function (target, loadingClass) {
            target.find('.' + loadingClass).remove();
        },
        onStart: function (e, files) {
            var me = this;
            this.loadingClass = 'giftcard-image-upload-loading';
            for (var i = 0; i < files.length; i++) {
                var fileType = me.getFileType(files[i].name);
                if ($.inArray(fileType, this.allowType) !== -1) {
                    this.addAjaxLoading(this.uploadContent, this.loadingClass);
                    GifCardTemplate.form.removeCurrentUploaded();

                }
                else {
                    alert('Do not allow file type')
                }
            }
        },
        onFileComplete: function (e, file, response) {
            if (response.trim() !== "" && response.toLowerCase().indexOf("error") == -1) {
                try {
                    var json = JSON.parse(response);
                    if (json.image) {
                        this.removeAjaxLoading(this.uploadContent, this.loadingClass);
                        GifCardTemplate.form.currentUploaded.val(json.image);
                        this.uploadedImg = $('<img>', {
                            src: this.baseImageUrl + json.image
                        })

                        this.uploadedImg.css({
                            width: this.uploadContent.width() + 'px',
                            height: 'auto'
                        })
                        this.addMoveEvent();
                        this.updateContent();
                        this.showAnotherUpload();
                    }

                } catch (e) {
                }

            }
        },
        addMoveEvent: function () {
            var me = this;
            var el = this.uploadedImg;
            el.draggable({
                    drag: function () {
                        if (!me.imgPosition.initOffset) {
                            var offset = $(this).offset();
                            var xPos = offset.left;
                            var yPos = offset.top;
                            me.imgPosition = {
                                left: xPos,
                                top: yPos
                            }
                            me.imgPosition.initOffset = true;
                        }
                    },
                    stop: function () {
                        var offset = $(this).offset();
                        var xPos = offset.left;
                        var yPos = offset.top;
                        me.imgPosition = {
                            left: xPos - me.imgPosition.left,
                            top: yPos - me.imgPosition.top
                        }
                        me.imgPosition.initOffset = false;
                    }
                }
            )
        },
        getFileType: function (filename) {
            var parts = filename.split('.');
            return parts[parts.length - 1];
        },
        updateContent: function () {
            var uploaded = $('<div>', {
                class: 'uploaded'
            });
            uploaded.append(this.uploadedImg);
            this.uploadContent.html(uploaded);
            GifCardTemplate.design.items.selectedItem = this.uploadContent.parent();
            GifCardTemplate.design.items.hasImgUploaded = true;
        },
        showAnotherUpload: function () {
            if (this.uploadedImg) {
                this.uploadAnotherEl.show();
                this.cropResizeArea.show();
            }
        },
        initObservers: function () {
            var me = this;
            this.backDesignEl.click(function () {
                GifCardTemplate.design.items.hasImgUploaded = false;
                GifCardTemplate.design.items.itemUploadWrapper.hide();
                GifCardTemplate.design.items.itemWrapper.show();
            });
            this.rotateBtn.click(function () {
                me.angle += 90;
                me.uploadedImg.rotate({
                    //angle: angle,
                    animateTo: me.angle,
                    callback: function () {
                    }
                });
            });
            this.zoomInBtn.click(function (e) {
                if (!me.zoomWidthIncrement) {
                    me.zoomWidthIncrement = me.uploadedImg.width() * 1 / 3;
                    me.zoomHeightIncrement = me.uploadedImg.height() * 1 / 3;
                }
                var zoomWidthIncrement = me.zoomWidthIncrement;
                var zoomHeightIncrement = me.zoomHeightIncrement;
                me.uploadedImg.css({
                    width: me.uploadedImg.width() + zoomWidthIncrement + 'px',
                    height: me.uploadedImg.height() + zoomHeightIncrement + 'px'
                });
                me.centerImg(me.uploadedImg);
                e.preventDefault();
            });
            this.zoomOutBtn.click(function (e) {
                if (!me.zoomWidthIncrement) {
                    me.zoomWidthIncrement = me.uploadedImg.width() * 1 / 3;
                    me.zoomHeightIncrement = me.uploadedImg.height() * 1 / 3;
                }
                var zoomWidthIncrement = me.zoomWidthIncrement;
                var zoomHeightIncrement = me.zoomHeightIncrement;
                me.uploadedImg.css({
                    width: me.uploadedImg.width() - zoomWidthIncrement + 'px',
                    height: me.uploadedImg.height() - zoomHeightIncrement + 'px'
                });
                me.centerImg(me.uploadedImg);
                e.preventDefault();
            });
        },
        centerImg: function (img) {
            var parent = img.parent();
            var top = parent.height() / 2 - img.height() / 2 + this.imgPosition.top / 2;
            var left = parent.width() / 2 - img.width() / 2 + this.imgPosition.left / 2;
            img.css({
                'top': top,
                'left': left
            })
        }


    }
    GifCardTemplate.form = {
        initialize: function (config) {
            this.productImgEl = $(config.productImgEl);
            this.changeFormatUrl = config.changeFormatUrl;
            this.removeImageUrl = config.removeImageUrl;
            this.formatEls = $(config.formatEls);
            this.uploadedHtml = $(config.uploadedHtml);
            this.currentItem = $(config.currentItem);
            this.currentUploaded = $(config.currentUploaded);
            this.currentFormat = $(config.currentFormat);
            this.giftMessageEl = $(config.giftMessageEl);
            this.productMessageEl = $(config.productMessageEl);
            this.giftCardAmountEl = $(config.giftCardAmountEl);
            this.productAmountEl = $(config.productAmountEl);
            this.productIdEl = $(config.productIdEl);
            this.videoUrlEl = $(config.videoUrlEl);
            this.initObservers();
        },
        removeCurrentUploaded: function () {
            var requestOptions = {
                parameters: {
                    current_uploaded: this.currentUploaded.val()
                }
            };
            GifCardTemplate.processor.Request(this.removeImageUrl, requestOptions);
        },
        initObservers: function () {
            var me = this;
            this.formatEls.click(function () {
                var el = this;
                me.currentFormat.val(el.value);
                var currentDesign = GifCardTemplate.design.currentDesign;
                var requestOptions = {
                    parameters: {
                        product_id: me.productIdEl.val(),
                        format_type: el.value,
                        design_type: currentDesign ? currentDesign.val() : ''
                    }
                };
                me.changeFormat(requestOptions)

            });
            this.giftMessageEl.keyup(function () {
                var el = this;
                var previewPopup = GifCardTemplate.form.previewPopup;
                var previewMsgBox = previewPopup.find('.gift-message-box .detail-message');
                me.productMessageEl.html(el.value);
                if (previewMsgBox) {
                    previewMsgBox.html(el.value);
                }
            })
            this.giftCardAmountEl.change(function () {
                var price = giftCardPrice.loadPrice();
                me.productAmountEl.html(price);
                var previewPopup = GifCardTemplate.form.previewPopup;
                var previewPriceBox = previewPopup.find('.gift-price-box .price-box');
                if (previewPriceBox) {
                    previewPriceBox.html(price);
                }
            })

        },
        addUploadTrigger: function (el) {
            el.click(function () {
                GifCardTemplate.popup.showPopup();
                GifCardTemplate.design.items.showUploadArea();
            })
        },
        updateProductHtml: function (html) {
            if (!this.productImgEl) {
                return;
            }
            this.productImgEl.html(html);
            var customUpload = this.productImgEl.find('.custom-upload');
            if (customUpload.length) {
                this.isUpdated = true;
                this.addUploadTrigger(customUpload);
            }
            else {
                var productImage = this.productImgEl;
                if (productImage) {
                    if (this.previewPopup) {
                        this.previewPopup.each(function () {
                            $(this).remove()
                        })
                    }
                    this.previewPopup = productImage.parent().clone();
                    $(this.previewPopup).popup({
                        transition: 'all 0.3s',
                        scrolllock: false
                    });
                    var templateImg = productImage.find('.template-img');
                    templateImg.click(function () {
                        $(this.previewPopup).popup('show');

                    }.bind(this));
                }
                this.isUpdated = false;
            }
            this.updateData();

        },
        updateData: function () {
            var item = this.productImgEl.find('.template-img')[0];
            var uploaded = this.productImgEl.find('.custom-upload');
            if (uploaded) {
                this.uploadedHtml.val(uploaded.html());
            }
            else {
                this.uploadedHtml.val('')
            }
            if (item) {
                this.currentItem.val(item.id.replace('item-', ''));
            }
            else {
                this.currentItem.val('');
            }
        },
        changeFormat: function (requestOptions) {
            GifCardTemplate.processor.Request(this.changeFormatUrl, requestOptions);

        }
    }
    ;
    GifCardTemplate.form.items = {
        initialize: function (config) {
            this.itemEls = $(config.itemEls);
            this.videoEls = $(config.videoEls);
            this.selectedItem = '';
            this.initObservers();
            this.initItem();
        },
        initItem: function () {
            var me = this;
            $(document).ready(function () {
                me.itemEls.each(function () {
                    var item = $(this);
                    if (item.hasClass('active')) {
                        me.selectedItem = item.parent();
                        me.updateProductImg();
                    }
                });
            })
        },
        updateProductImg: function () {
            var itemEl = this.selectedItem.clone();
            var itemImg = itemEl.find('img');
            if (itemImg.hasClass('video-thumb')) {
                itemImg.hide();
                itemEl.find('iframe').show();
            }
            itemImg.attr('src', itemImg.attr('source'));
            var uploadedContent = GifCardTemplate.design.upload.uploadContent;
            if (uploadedContent.find('.uploaded').length) {
                var templateImg = GifCardTemplate.form.productImgEl.find('.template-img');
                if (!this.templateImg || GifCardTemplate.form.isUpdated) {
                    this.templateImg = templateImg;
                    GifCardTemplate.form.isUpdated = false;
                }
            }
            else {
                this.templateImg = itemEl;
            }
            if (itemImg.hasClass('custom')) {
                GifCardTemplate.form.updateProductHtml(this.templateImg);
            }

            else {
                GifCardTemplate.form.updateProductHtml(itemEl);
            }
        },
        checkUploadFormat: function (item) {
            if (item.hasClass('custom')) {
                return true;
            }
            return false;
        },
        initObservers: function () {
            var me = this;
            this.itemEls.click(function () {
                var el = $(this);
                me.selectedItem = el.parent();
                me.activeItem(el);
                if (me.checkUploadFormat(el)) {
                    GifCardTemplate.popup.showPopup();
                }
                GifCardTemplate.form.videoUrlEl.val('')

            });
            this.videoEls.click(function () {
                var el = $(this);
                me.selectedItem = el.parent();
                me.activeItem(el);
                if (me.checkUploadFormat(el)) {
                    GifCardTemplate.popup.showPopup();
                }
                GifCardTemplate.form.videoUrlEl.val('')
            });
        },
        activeItem: function (item) {
            this.itemEls.each(function () {
                var el = $(this);
                if (item.is(el)) {
                    el.addClass('active');
                }
                else {
                    el.removeClass('active');
                }
            });
            this.videoEls.each(function () {
                var el = $(this);
                if (item.is(el)) {
                    el.addClass('active');
                }
                else {
                    el.removeClass('active');
                }
            });
            this.updateProductImg();
        }
    }
    GifCardTemplate.form.upload = {
        initialize: function (config) {
            this.uploadForm = $(config.uploadForm);
            this.uploadEl = $(config.uploadEl);
            this.uploadLabel = config.uploadLabel;
            this.allowType = config.allowType;
            this.selectedFrame = config.selectedFrame;
            this.uploadImageUrl = config.uploadImageUrl;
            this.removeImageUrl = config.removeImageUrl;
            this.baseImageUrl = config.baseImageUrl;
            this.loadingClass = '.giftcard-form-upload-loading';
            this.uploadedImg = '';
            this.customEl = '';
            this.initObservers();
        },
        initObservers: function () {
            var me = this;
            this.uploadEl.dropper({
                action: this.uploadImageUrl,
                maxSize: this.maxSize,
                label: this.uploadLabel,
                multiple: false
            })
                .on("start.dropper", me.onStart.bind(this))
                .on("fileComplete.dropper", me.onFileComplete.bind(this));
        },
        addAjaxLoading: function (target, loadingClass) {
            var loading = $('<div>', {
                class: loadingClass
            })
            target.append(loading);
        },
        removeAjaxLoading: function (target, loadingClass) {
            target.find('.' + loadingClass).remove();
        },
        onStart: function (e, files) {
            var me = this;
            this.loadingClass = 'giftcard-image-upload-loading';
            for (var i = 0; i < files.length; i++) {
                var fileType = me.getFileType(files[i].name);
                if ($.inArray(fileType, this.allowType) !== -1) {
                    this.addAjaxLoading($(this.selectedFrame), this.loadingClass);
                    if (me.checkIsCustom()) {
                        me.createCustomEl();
                    }
                    GifCardTemplate.form.removeCurrentUploaded();

                }
                else {
                    alert('Do not allow file type')
                }
            }
        },
        checkIsCustom: function () {
            var item = $(this.selectedFrame).find('img');
            return item.hasClass('custom') || item.hasClass('upload-frame');
        },
        getFileType: function (filename) {
            var parts = filename.split('.');
            return parts[parts.length - 1];
        },
        onFileComplete: function (e, file, response) {
            if (response.trim() !== "" && response.toLowerCase().indexOf("error") == -1) {
                this.removeAjaxLoading($(this.selectedFrame), this.loadingClass);
                try {
                    var json = JSON.parse(response);
                    if (json.image) {
                        GifCardTemplate.form.currentUploaded.val(json.image);
                        this.updateProductImg(json.image)
                    }

                } catch (e) {
                }

            }
        },
        createImage: function (name) {
            var imgEl = $('<img>', {
                src: this.baseImageUrl + name
            });
            return imgEl;
        },
        updateProductImg: function (imageName) {
            var me = this;
            var selectedFrame = $(this.selectedFrame).first();
            if (selectedFrame) {
                var image = this.createImage(imageName);
                GifCardTemplate.form.currentUploaded.val(imageName);
                if (this.checkIsCustom()) {
                    var uploaded = $('<div>', {
                        class: 'uploaded'
                    });
                    uploaded.html(image);
                    this.customEl.html(uploaded);
                    selectedFrame.append(this.customEl);
                    GifCardTemplate.form.updateProductHtml(selectedFrame);
                    this.resizeImage(image, this.customEl);
                    setTimeout(function () {
                        me.updatePopup(selectedFrame, image);
                    }, 100)
                }
                else {
                    selectedFrame.html(image);
                    GifCardTemplate.form.updateProductHtml(selectedFrame);
                }
            }
        },
        updatePopup: function (selectedFrame, image) {
            var popupFrame = GifCardTemplate.design.upload.uploadFrameEl;
            var popupUploadContent = GifCardTemplate.design.upload.uploadContent;
            var popupUploaded = GifCardTemplate.design.upload.uploadedImg;
            GifCardTemplate.design.items.selectedItem = selectedFrame.clone();
            GifCardTemplate.design.upload.uploadedImg = image.clone();
            popupFrame.attr('src', selectedFrame.find('img').attr('src'));
            if (popupUploaded) {
                popupUploaded.attr('src', image.attr('src'));
                popupUploaded.css({
                    width: popupUploadContent.width(),
                    height: 'auto'
                })
            }
            else {
                popupUploadContent.html(this.customEl.find('.uploaded').clone());
            }
            GifCardTemplate.popup.showPopup();
            GifCardTemplate.design.items.showUploadArea();
            GifCardTemplate.design.items.activeItem();
            GifCardTemplate.design.upload.addMoveEvent(this.uploadedImg);
            GifCardTemplate.design.upload.updateContent();
            GifCardTemplate.design.upload.showAnotherUpload();
        },
        createCustomEl: function () {
            if (!this.customEl) {
                if ($(this.selectedFrame).find('.custom-upload').length) {
                    var custom = $(this.selectedFrame).find('.custom-upload');
                    this.customEl = custom;
                }
                else {
                    var custom = $('<div>',
                        {
                            class: 'custom-upload'
                        }
                    );
                }
                this.customEl = custom;

            }
            return this.customEl;
        },
        resizeImage: function (image, parent) {
            var maxWidth = $(this.selectedFrame).width();
            var maxHeight = $(this.selectedFrame).height();
            var widthRatio = maxWidth / 585;
            var heightRatio = maxHeight / 302;
            var uploadWidth = widthRatio * 279;
            var uploadHeight = heightRatio * 179;
            var uploadTop = heightRatio * 62;
            var uploadLeft = widthRatio * 158;
            image.css({
                width: Math.ceil(uploadWidth),
                height: 'auto',
                maxWidth: 'none',
                maxHeight: 'none'
            })
            parent.css({
                width: Math.ceil(uploadWidth) + 'px',
                height: Math.ceil(uploadHeight) + 'px',
                top: Math.floor(uploadTop) + 'px',
                left: uploadLeft + 'px'
            })
        }
    }
    GifCardTemplate.form.video = {
        initialize: function (config) {
            this.videoFrame = config.videoFrame;
            this.videoForm = config.videoForm;
            this.videoInput = $(config.videoInput);
            this.videoButton = $(config.videoButton);
            this.loadingEl = $(config.loadingEl);
            this.processEmbedUrl = config.processEmbedUrl;
            this.initForm();
            this.initObservers();
        },
        initForm: function () {
            this.formValidation = new Validation(this.videoForm);
        },
        initObservers: function () {
            var me = this;
            this.videoButton.click(function () {
                me.videoInput.addClass('required-entry');
                if (!this.formValidation.validate()) {
                    me.videoInput.removeClass('required-entry');
                    return false;
                }
                this.processEmbedVideo();
            }.bind(this));
        },
        processEmbedVideo: function () {
            var me = this;
            $.ajax({
                url: this.processEmbedUrl,
                data: {
                    video_url: this.videoInput.val()
                },
                type: "POST",
                beforeSend: function () {
                    me.loadingEl.show();
                },
                success: function (response) {
                    me.loadingEl.hide();
                    try {
                        var response = response.evalJSON();
                        if (response.success) {
                            var embedUrl = response.embed_url;
                            GifCardTemplate.form.videoUrlEl.val(embedUrl);
                            $(me.videoFrame).attr('src', embedUrl)
                        }
                    } catch (e) {
                    }
                }
            });
        }
    }
    $.fn.scrollTo = function (target, options, callback) {
        if (typeof options == 'function' && arguments.length == 2) {
            callback = options;
            options = target;
        }
        var settings = $.extend({
            scrollTarget: target,
            offsetTop: 50,
            duration: 500,
            easing: 'swing'
        }, options);
        return this.each(function () {
            var scrollPane = $(this);
            var scrollTarget = (typeof settings.scrollTarget == "number") ? settings.scrollTarget : $(settings.scrollTarget);
            var scrollY = (typeof scrollTarget == "number") ? scrollTarget : scrollTarget.offset().top + scrollPane.scrollTop() - parseInt(settings.offsetTop);
            scrollPane.animate({scrollTop: scrollY}, parseInt(settings.duration), settings.easing, function () {
                if (typeof callback == 'function') {
                    callback.call(this);
                }
            });
        });
    }
})
(jQuery)