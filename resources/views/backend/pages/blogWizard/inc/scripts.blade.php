<script>
    'use strict';


    fillInputFields();
    setLocalWizardData();
    handleBlogWizardId();
    handleSelectionClick();
    populateKeywordsData();
    populateTitlesData();
    populateImagesData();
    populateOutlinesData();
    populateArticleData();

    // is enable image
    var is_enable_image = "{{getSetting('generate_image') == '1' ? true : false}}";
    //  active steps 
    $(".progressbar-list").removeClass("is-active");
    for (let index = 0; index < wizardFormData.activeStep; index++) {
        $(".progressbar-list").eq(index).addClass("is-active");
    }

    $(".tt-single-fieldset").removeClass("is-active");
    $(".tt-single-fieldset").eq(wizardFormData.activeStep - 1).addClass("is-active");

    $(".tt-fieldset-data").removeClass("is-active");
    $(".tt-fieldset-data").eq(wizardFormData.activeStep - 1).addClass("is-active");
    console.log(wizardFormData.activeStep);

    // btn-next-step
    $('.btn-next-step').on('click', function() {
        let activeStep = wizardFormData.activeStep;
        wizardFormData.activeStep = activeStep + 1;
        console.log(activeStep);
        // set data contidionally here after checking the steps 
        if (activeStep == 1) {
            wizardFormData.keywords.data.keywords = $('#keywordsStep').val();
            wizardFormData.keywords.data.topic = $('#topicStepKeyword').val();
        } else if (activeStep == 2) {
            // do nothing here
            if(is_enable_image == false){
                wizardFormData.outlines.data.title = wizardFormData.images.data.title;
                wizardFormData.outlines.data.keywords = $('#keywordsStepTitle').val();
            }
        } else if (activeStep == 3) {

            if(is_enable_image == false){
                wizardFormData.article.data.title = $('#outlineStepTitle').val();
                wizardFormData.article.data.keywords = $('#outlineStepKeywords').val();
            }else{
                wizardFormData.outlines.data.title = wizardFormData.images.data.title;
                wizardFormData.outlines.data.keywords = $('#keywordsStepTitle').val();
            }
        } else if (activeStep == 4) {
            wizardFormData.article.data.title = $('#outlineStepTitle').val();
            wizardFormData.article.data.keywords = $('#outlineStepKeywords').val();
        }

        handleBlogWizardId();
        fillInputFields();
        setLocalWizardData();

        //  active steps 
        $(".progressbar-list").eq(activeStep).addClass("is-active");

        $(".tt-single-fieldset").removeClass("is-active");
        $(".tt-single-fieldset").eq(activeStep).addClass("is-active");

        $(".tt-fieldset-data").removeClass("is-active");
        $(".tt-fieldset-data").eq(activeStep).addClass("is-active");
        //  active steps

        // hide data next button
        $('.data-next-btn').addClass('d-none');
    });

    // btn-prev-step
    $('.btn-prev-step').on('click', function() {
        let activeStep = wizardFormData.activeStep - 1;
        wizardFormData.activeStep = activeStep;

        // set data contidionally here after checking the steps

        // set data contidionally here after checking the steps

        //  active steps 
        $(".progressbar-list").eq(activeStep).removeClass("is-active");

        $(".tt-single-fieldset").removeClass("is-active");
        $(".tt-single-fieldset").eq(activeStep - 1).addClass("is-active");

        $(".tt-fieldset-data").removeClass("is-active");
        $(".tt-fieldset-data").eq(activeStep - 1).addClass("is-active");
        //  active steps 

        $("input[type='radio']").prop('checked', false);
        setLocalWizardData();
    });

    // generate keywords
    $('.stepKeywordForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let url = '{{ route('blog.wizard.generateKeywords') }}';
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            method: 'POST',
            url: url,
            data: form.serialize(),
            beforeSend: function() {
                $('.keyword-text').html(TT.localize.pleaseWait);
                $('.btn-create-content').prop('disabled', true);
                $('.btn-create-content .tt-text-preloader').removeClass('d-none');
            },
            complete: function() {
                $('.keyword-text').html('{{ localize('Generate Keywords') }}');
                $('.btn-create-content .tt-text-preloader').addClass('d-none');
            },
            success: function(data) {
                if (data.status == 200) {
                    // 
                    $('.used-words-percentage').empty();
                    $('.keywords-data').empty();

                    $('.keywords-data').html(data['output']);
                    handleKeywordsOnChange();

                    $('.used-words-percentage').append(data.usedPercentage);
                    $('.btn-create-content').prop('disabled', false);

                    wizardFormData.aiBlogWizardId = data.ai_blog_wizard_id;
                    wizardFormData.keywords.data.topic = $('#topicStepKeyword').val();
                    wizardFormData.keywords.data.generatedKeywords = data.keywords;
                    handleBlogWizardId();

                    // set wizardFormData in localstorage 
                    setLocalWizardData();
                } else {
                    $('.btn-create-content').prop('disabled', false);
                    if (data.message) {
                        notifyMe('error', data.message);
                    } else {
                        notifyMe('error', '{{ localize('Something went wrong') }}');
                    }
                }
            },
            error: function(data) {
                $('.btn-create-content').prop('disabled', false);
                if (data.status == 400 && data.message) {
                    notifyMe('error', data.message);
                } else {
                    notifyMe('error', '{{ localize('Something went wrong') }}');
                }
            }
        });
    })



    // generate title
    $('.stepTitleForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let url = '{{ route('blog.wizard.generateTitles') }}';
        let text = $('.btn-create-content').data('text');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            method: 'POST',
            url: url,
            data: form.serialize(),
            beforeSend: function() {
                $('.title-text').html(TT.localize.pleaseWait);
                $('.btn-create-content').prop('disabled', true);
                $('.btn-create-content .tt-text-preloader').removeClass('d-none');
            },
            complete: function() {
                $('.title-text').html(
                    '{{ localize('Generate Titles') }}');
                $('.btn-create-content .tt-text-preloader').addClass('d-none');
            },
            success: function(data) {
                if (data.status == 200) {
                    // 
                    $('.used-words-percentage').empty();
                    $('.titles-data').empty();

                    $('.titles-data').html(data['output']);

                    $('.used-words-percentage').append(data.usedPercentage);
                    $('.btn-create-content').prop('disabled', false);

                    wizardFormData.aiBlogWizardId = data.ai_blog_wizard_id;
                    wizardFormData.titles.data.topic = $('#topicStepTitle').val();
                    wizardFormData.titles.data.generatedTitles = data.titles;
                    handleBlogWizardId();
                    handleSelectionClick();
                    // set wizardFormData in localstorage 
                    setLocalWizardData();
                } else {
                    $('.btn-create-content').prop('disabled', false);
                    if (data.message) {
                        notifyMe('error', data.message);
                    } else {
                        notifyMe('error', '{{ localize('Something went wrong') }}');
                    }
                }
            },
            error: function(data) {
                $('.btn-create-content').prop('disabled', false);
                if (data.status == 400 && data.message) {
                    notifyMe('error', data.message);
                } else {
                    notifyMe('error', '{{ localize('Something went wrong') }}');
                }
            }
        });
    })

    // generate image
    $('.stepImageForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let url = '{{ route('blog.wizard.generateImages') }}';
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            method: 'POST',
            url: url,
            data: form.serialize(),
            beforeSend: function() {
                $('.image-text').html(TT.localize.pleaseWait);
                $('.btn-create-content').prop('disabled', true);
                $('.btn-create-content .tt-text-preloader').removeClass('d-none');
            },
            complete: function() {
                $('.image-text').html(
                    '{{ localize('Generate Images') }}');
                $('.btn-create-content .tt-text-preloader').addClass('d-none');
            },
            success: function(data) {
                if (data.status == 200) {
                    // 
                    $('.used-images-percentage').empty();
                    $('.images-data').empty();

                    $('.images-data').html(data['images']);

                    $('.used-images-percentage').append(data.usedPercentage);
                    $('.btn-create-content').prop('disabled', false);

                    wizardFormData.images.data.generatedImages = data.imageIds;
                    handleSelectionClick();
                    // set wizardFormData in localstorage 
                    setLocalWizardData();
                } else {
                    $('.btn-create-content').prop('disabled', false);
                    if (data.message) {
                        notifyMe('error', data.message);
                    } else {
                        notifyMe('error', '{{ localize('Something went wrong') }}');
                    }
                }
            },
            error: function(data) {
                $('.btn-create-content').prop('disabled', false);
                if (data.status == 400 && data.message) {
                    notifyMe('error', data.message);
                } else {
                    notifyMe('error', '{{ localize('Something went wrong') }}');
                }
            }
        });
    })

    // generate outline
    $('.stepOutlineForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let url = '{{ route('blog.wizard.generateOutlines') }}';
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            method: 'POST',
            url: url,
            data: form.serialize(),
            beforeSend: function() {
                $('.outline-text').html(TT.localize.pleaseWait);
                $('.btn-create-content').prop('disabled', true);
                $('.btn-create-content .tt-text-preloader').removeClass('d-none');
            },
            complete: function() {
                $('.outline-text').html(
                    '{{ localize('Generate Outlines') }}');
                $('.btn-create-content .tt-text-preloader').addClass('d-none');
            },
            success: function(data) {
                if (data.status == 200) {
                    // 
                    $('.used-words-percentage').empty();
                    $('.outlines-data').empty();

                    $('.outlines-data').html(data['output']);

                    $('.used-words-percentage').append(data.usedPercentage);
                    $('.btn-create-content').prop('disabled', false);

                    wizardFormData.outlines.data.generatedOutlines = data.outlines;
                    handleSelectionClick();
                    // set wizardFormData in localstorage 
                    setLocalWizardData();
                } else {
                    $('.btn-create-content').prop('disabled', false);
                    if (data.message) {
                        notifyMe('error', data.message);
                    } else {
                        notifyMe('error', '{{ localize('Something went wrong') }}');
                    }
                }
            },
            error: function(data) {
                $('.btn-create-content').prop('disabled', false);
                if (data.status == 400 && data.message) {
                    notifyMe('error', data.message);
                } else {
                    notifyMe('error', '{{ localize('Something went wrong') }}');
                }
            }
        });
    })

    // generate article
    $('.stepArticleForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let url = '{{ route('blog.wizard.initArticle') }}';

        $('.article-content').empty();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            method: 'POST',
            url: url,
            data: form.serialize(),
            beforeSend: function() {
                $('.btn-create-text').html(TT.localize.pleaseWait);
                $('.btn-create-content').prop('disabled', true);
                $('.btn-create-content .tt-text-preloader').removeClass('d-none');
                $('.edit-blog-btn').addClass(
                    'd-none');
            },
            success: function(data) {
                let urlEvent = '{{ route('blog.wizard.generateArticle') }}'
                    TT.eventSource = new EventSource(`${urlEvent}`, {
                        withCredentials: true
                    });
                if (data.status == 200) {
                    $('.btn-stop-content').prop('disabled', false);
                    $('.used-words-percentage').empty().append(data.usedPercentage);
                    $('.article-data').empty().html(data['article']);
                    $('.btn-create-content').prop('disabled', false);

                    TT.eventSource.onmessage = eventOnSuccess;
                    TT.eventSource.onerror = eventOnError;

                    wizardFormData.article.data.generatedArticle = data.articleId;
                    setLocalWizardData();

                    // article content
                    // let url = '{{ route('blog.wizard.generateArticle') }}'
                    // const eventSource = new EventSource(`${url}`, {
                    //     withCredentials: true
                    // });

                    TT.eventSource.onmessage = function(e) {
                        if (e.data == "[DONE]") {
                            $('.article-text').html(
                                '{{ localize('Generate Article') }}');
                            $('.btn-create-content .tt-text-preloader').addClass(
                                'd-none');
                            $('.edit-blog-btn').removeClass(
                                'd-none');
                            initFeather();
                            TT.eventSource.close();
                        } else {
                            let txt = e.data;
                            if (txt !== undefined) {
                                txt = txt.replace(/(?:\r\n|\r|\n)/g, '<br>');

                                let oldValue = '';

                                oldValue += $('.article-content').html();

                                let value = oldValue + txt;

                                if(value.indexOf('****') !== -1) {
                                    value = value.replace(/\*\*\*(.*?)\*\*\*/g,
                                    '<h6 class="mb-0 mt-3 h6">$1</h6>');
                                }
                                else if(value.indexOf('***') !== -1) {
                                    value = value.replace(/\*\*\*(.*?)\*\*\*/g,
                                    '<h5 class="mb-0 mt-3 h5">$1</h5>');
                                }
                                else if(value.indexOf('**') !== -1) {
                                    value = value.replace(/\*\*(.*?)\*\*/g,
                                    '<h4 class="mb-0 mt-3 h4">$1</h4>');
                                }
                                
                                $('.article-content').html(value);
                            }
                        }
                    };

                    TT.eventSource.onerror = function(e) {
                        $('.article-text').html(
                            '{{ localize('Generate Article') }}');
                        $('.btn-create-content .tt-text-preloader').addClass(
                            'd-none');
                        TT.eventSource.close();
                    };
                } else {
                    $('.article-text').html(
                        '{{ localize('Generate Article') }}');
                    $('.btn-create-content .tt-text-preloader').addClass(
                        'd-none');
                    notifyMe('error', data.message);
                }
            },
            error: function(data) {
                $('.btn-create-content').prop('disabled', false);
                if (data.status == 400 && data.message) {
                    notifyMe('error', data.message);
                } else {
                    notifyMe('error', '{{ localize('Something went wrong') }}');
                }

                $('.article-text').html(
                    '{{ localize('Generate Article') }}');
                $('.btn-create-content .tt-text-preloader').addClass(
                    'd-none');
            }
        });
    })

    function eventOnSuccess(e) {

        $('.editor').summernote('enable');

        if (e.data == "[DONE]") {
             resetGenerateButton();
            notifyMe('success',
                '{{ localize('Contents generated successfully') }}');
            TT.eventSource.close();
        } else {
            let txt = e.data;
            if (txt !== undefined) {
                let oldValue = '';
                if ($('.new-msg-loader:first .tt-message-text').find(
                        '.tt-text-preloader').length !== 0) {
                    $('.new-msg-loader:first .tt-message-text')
                        .empty();
                    $('.new-msg-loader').first().removeClass(
                        'd-none');

                } else {
                    oldValue += $('.note-editable').html();
                }

                let value = oldValue +
                    txt;

                value = value.replace(/\*\*(.*?)\*\*/g,
                    '<h3 class="mb-0">$1</h3>');

                $('.new-msg-loader:first .tt-message-text').html(value);
                $('.note-editable').html(value);
            }
        }
    }

    function eventOnError(e) 
    {
        TT.eventSource.close();
        resetGenerateButton();
        notifyMe('error',
            '{{ localize('Something wrong happened. Please try again.') }}');
    }
    // Forcefully stop generating content
    $('.btn-stop-content').on('click', function(e) {
            e.preventDefault();
            TT.eventSource.close();
             resetGenerateButton();
            notifyMe('info',
                '{{ localize('Articale generation has been stopped.') }}');
            updatBalanceAfterStopGeneration();
    });
    function resetGenerateButton() {
            $('.btn-create-text').html(TT.localize.createContent);
            $('.btn-create-content .tt-text-preloader').addClass('d-none');
            $('.edit-blog-btn').removeClass('d-none');                            
            $('.btn-create-content').prop('disabled', false);
            $('.btn-stop-content').prop('disabled', true);
            initFeather();
    }
    function updatBalanceAfterStopGeneration(){
        $.ajax({
            headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                method: 'POST',
                url: '{{ route('blog.wizard.update_balace') }}',              
                success: function(data) {

                },
                error:function()
                {

                }
        })
    }
    // content-form submit -- update contents
    $('.content-form-blog-wizard').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let data = form.serialize();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            method: 'POST',
            url: '{{ route('blog.wizard.update') }}',
            data: data,
            beforeSend: function() {
                $('.content-form-submit').prop('disabled', true);
            },
            complete: function() {
                $('.content-form-submit').prop('disabled', false);
            },
            success: function(data) {
                if (data.status == 200) {
                    notifyMe('success',
                        '{{ localize('Blog updated successfully') }}');
                } else {
                    notifyMe('error', '{{ localize('Something went wrong') }}');
                }
            },
            error: function(data) {
                notifyMe('error', '{{ localize('Something went wrong') }}');
            }
        });
    });

    // copy contents 
    $(".copyBtn").on("click", function() {
        var type = $(this).data('type');

        var html = document.querySelector('.note-editable');
        var msg = '{{ localize('Blog has been copied successfully') }}';

        const selection = window.getSelection();
        const range = document.createRange();
        range.selectNodeContents(html);
        selection.removeAllRanges();
        selection.addRange(range);
        document.execCommand('copy');
        window.getSelection().removeAllRanges()
        notifyMe('success', msg);
    });


</script>
