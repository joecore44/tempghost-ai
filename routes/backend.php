<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AzureT2SController;
use App\Http\Controllers\Backend\StaffsController;
use App\Http\Controllers\Backend\ConstantController;
use App\Http\Controllers\Backend\faq\FaqsController;
use App\Http\Controllers\Backend\LanguageController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\AI\AiChatController;
use App\Http\Controllers\Backend\CustomersController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\CurrenciesController;
use App\Http\Controllers\Backend\MultiOpenAiController;
use App\Http\Controllers\Backend\Pages\PagesController;
use App\Http\Controllers\Backend\Roles\RolesController;
use App\Http\Controllers\Backend\SubscribersController;
use App\Http\Controllers\Backend\AI\GenerateS2TController;
use App\Http\Controllers\Backend\AI\GenerateT2SController;
use App\Http\Controllers\Backend\AI\AiChatExpertController;
use App\Http\Controllers\Backend\AI\VoiceSettingController;
use App\Http\Controllers\Backend\BlogSystem\TagsController;
use App\Http\Controllers\Backend\Folders\FoldersController;
use App\Http\Controllers\Backend\Reports\ReportsController;
use App\Http\Controllers\Affiliate\ConfigurationsController;
use App\Http\Controllers\Backend\AI\GenerateCodesController;
use App\Http\Controllers\Backend\BlogSystem\BlogsController;
use App\Http\Controllers\Backend\AI\GenerateImagesController;
use App\Http\Controllers\Backend\Appearance\FooterController;
use App\Http\Controllers\Backend\Appearance\HeaderController;
use App\Http\Controllers\Backend\Projects\ProjectsController;
use App\Http\Controllers\Affiliate\EarningHistoriesController;
use App\Http\Controllers\Affiliate\WithdrawRequestsController;
use App\Http\Controllers\Backend\Appearance\AboutUsController;
use App\Http\Controllers\Affiliate\AffiliateOverviewController;
use App\Http\Controllers\Affiliate\AffiliatePaymentsController;
use App\Http\Controllers\Backend\AI\GenerateContentsController;
use App\Http\Controllers\Backend\AI\GenerateSdImagesController;
use App\Http\Controllers\Backend\Appearance\HomepageController;
use App\Http\Controllers\Backend\Templates\TemplatesController;
use App\Http\Controllers\Backend\Offline\PaymentRequestController;
use App\Http\Controllers\Backend\Newsletters\NewslettersController;
use App\Http\Controllers\Backend\Appearance\ClientFeedbackController;
use App\Http\Controllers\Backend\BlogSystem\BlogCategoriesController;
use App\Http\Controllers\Backend\Customer\CustomerSettingsController;
use App\Http\Controllers\Backend\MediaManager\MediaManagerController;
use App\Http\Controllers\Backend\Templates\CustomTemplatesController;
use App\Http\Controllers\Backend\Contacts\ContactUsMessagesController;
use App\Http\Controllers\Backend\Subscription\SubscriptionsController;
use App\Http\Controllers\Backend\Offline\OfflinePaymentMethodController;
use App\Http\Controllers\Backend\Templates\PromptsConfigurationController;
use App\Http\Controllers\Affiliate\AffiliatePayoutConfigurationsController;
use App\Http\Controllers\Backend\FileStorageManagerController;
use App\Http\Controllers\Backend\AI\AiChatPromptsController;
use App\Http\Controllers\Backend\AI\BlogWizardController;
use App\Http\Controllers\Backend\Offline\CustomerReSubmitRequestController;
use App\Http\Controllers\Backend\PaymentGateway\PaymentGatewayController;
use App\Http\Controllers\Backend\Subscription\SubscriptionSettingsController;
use App\Http\Controllers\Backend\Templates\CustomTemplateCategoriesController;
use App\Http\Controllers\Backend\UtilityController;
use App\Http\Controllers\Backend\WrNotificationContoller;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\Backend\SystemUpdateController;
use App\Http\Controllers\Backend\Payments\Paypal\PaypalController;
use App\Http\Controllers\Backend\Subscription\SubscriptionStatusController;
use App\Http\Controllers\Backend\Publisher\WordpressController;
use App\Http\Controllers\FilePermissionController;

/*
|--------------------------------------------------------------------------
| Backend Routes
|--------------------------------------------------------------------------
*/

# common routes
Route::group(['prefix' => 'backend'], function () {
    # change settings
    Route::post('/change-currency', [CurrenciesController::class, 'changeCurrency'])->name('backend.changeCurrency');
    Route::post('/change-language', [LanguageController::class, 'changeLanguage'])->name('backend.changeLanguage');

    # package templates
    Route::get('/get-package-templates', [SubscriptionsController::class, 'getPackageTemplates'])->name('subscriptions.getPackageTemplates');
});

# dashboard routes
Route::group(
    ['prefix' => '', 'middleware' => ['isBanned']],
    function () {
        Route::group(
            ['prefix' => 'dashboard', 'middleware' => ['auth', 'verified']],
            function () {
                # dashboard
                Route::get('/', [DashboardController::class, 'index'])->name('writebot.dashboard');
                Route::get('/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
                Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update')->middleware('demo');

                # subscriptions routes
                Route::group(['prefix' => 'subscriptions', 'middleware' => ['auth']], function () {
                    # subscriptions
                    Route::get('/', [SubscriptionsController::class, 'index'])->name('subscriptions.index');
                    
                    Route::get('/delete/{id}', [SubscriptionsController::class, 'delete'])->name('subscriptions.delete')->middleware('demo');
                    Route::get('/get-packages', [SubscriptionsController::class, 'indexTypePackages'])->name('subscriptions.indexTypePackages');
                    Route::post('/update-packages', [SubscriptionsController::class, 'update'])->name('subscriptions.update')->middleware('demo');
                    Route::get('/get-templates', [SubscriptionsController::class, 'getTemplates'])->name('subscriptions.getTemplates');

                    Route::post('/update-package-templates', [SubscriptionsController::class, 'updateTemplates'])->name('subscriptions.updateTemplates')->middleware('demo');
                    Route::get('/copy-package', [SubscriptionsController::class, 'copyPackage'])->name('subscriptions.copyPackage');
                    Route::post('/copy-package', [SubscriptionsController::class, 'newPackage'])->name('subscriptions.newPackage')->middleware('demo');

                    # histories 
                    Route::get('/histories', [SubscriptionsController::class, 'indexHistory'])->name('subscriptions.histories.index');
                    Route::post('/recurring/status', [SubscriptionStatusController::class, 'subscriptionStatusRecurringPayment'])->name('subscriptions.recurring.status');
                });


                # affiliate routes
                Route::group(['prefix' => 'affiliate', 'middleware' => ['auth', 'affiliate']], function () {
                    # configurations 
                    Route::get('/configurations', [ConfigurationsController::class, 'index'])->name('affiliate.configurations')->middleware('admin');

                    # overview 
                    Route::get('/overview', [AffiliateOverviewController::class, 'index'])->name('affiliate.overview')->middleware('customer');
                    Route::get('/configure-payouts', [AffiliatePayoutConfigurationsController::class, 'index'])->name('affiliate.payout.configure')->middleware('customer');
                    Route::post('/configure-payouts', [AffiliatePayoutConfigurationsController::class, 'store'])->name('affiliate.payout.configureStore')->middleware('customer')->middleware('demo');

                    # withdraw
                    Route::get('/withdraw-requests', [WithdrawRequestsController::class, 'index'])->name('affiliate.withdraw.index');
                    Route::post('/withdraw-requests', [WithdrawRequestsController::class, 'store'])->name('affiliate.withdraw.store')->middleware('demo');
                    Route::post('/update-requests', [WithdrawRequestsController::class, 'update'])->name('affiliate.withdraw.update')->middleware('demo');

                    # earning histories
                    Route::get('/earning-histories', [EarningHistoriesController::class, 'index'])->name('affiliate.earnings.index');

                    # payments
                    Route::get('/payments', [AffiliatePaymentsController::class, 'index'])->name('affiliate.payments.index');
                });

                # document routes
                Route::group(['prefix' => 'documents', 'middleware' => ['auth']], function () {
                    # folders 
                    Route::get('/folders', [FoldersController::class, 'index'])->name('folders.index');
                    Route::post('/add-folder', [FoldersController::class, 'store'])->name('folders.store')->middleware('demo');
                    Route::get('/folders/{slug}', [FoldersController::class, 'show'])->name('folders.show');
                    Route::get('/edit-folder/{slug}', [FoldersController::class, 'edit'])->name('folders.edit');
                    Route::post('/update-folder', [FoldersController::class, 'update'])->name('folders.update')->middleware('demo');
                    Route::get('/folders/delete/{id}', [FoldersController::class, 'delete'])->name('folders.delete');

                    # projects
                    Route::get('/projects', [ProjectsController::class, 'index'])->name('projects.index');
                    Route::get('/edit-project/{slug}', [ProjectsController::class, 'edit'])->name('projects.edit');
                    Route::post('/update-project', [ProjectsController::class, 'update'])->name('projects.update')->middleware('demo');
                    Route::get('/projects/delete/{id}', [ProjectsController::class, 'delete'])->name('projects.delete');

                    # project ajax
                    Route::post('/move-to-folder-modal', [ProjectsController::class, 'moveToFolderModalOpen'])->name('projects.moveToFolderModal');
                    Route::post('/move-to-folder', [ProjectsController::class, 'moveToFolder'])->name('projects.moveToFolder');
                });

                # templates routes
                Route::group(['prefix' => 'templates', 'middleware' => ['auth']], function () {

                    # templates
                    Route::get('/', [TemplatesController::class, 'index'])->name('templates.index');
                    Route::get('/favorites', [TemplatesController::class, 'indexFavorite'])->name('templates.favorites');
                    Route::post('/favorites', [TemplatesController::class, 'updateFavorite'])->name('templates.updateFavorite');
                    Route::get('/popular', [TemplatesController::class, 'indexPopular'])->name('templates.popular');
                    Route::get('/edit/{id}', [TemplatesController::class, 'edit'])->name('templates.edit');
                    Route::get('/delete/{id}', [TemplatesController::class, 'delete'])->name('templates.delete')->middleware('demo');
                    Route::post('/update', [TemplatesController::class, 'update'])->name('templates.update')->middleware('demo');

                    # prompts
                    Route::get('/prompts', [PromptsConfigurationController::class, 'index'])->name('templates.prompts.index');
                    Route::post('/prompts', [PromptsConfigurationController::class, 'show'])->name('templates.prompts.show');
                    Route::post('/update-prompts', [PromptsConfigurationController::class, 'store'])->name('templates.prompts.store');

                    # import templates
                    Route::get('/import-templates', [TemplatesController::class, 'store'])->name('templates.store');

                    # generate contents
                    Route::post('/generate', [GenerateContentsController::class, 'generate'])->name('templates.generate');
                    Route::get('/process-generate', [GenerateContentsController::class, 'processContents'])->name('templates.processContents');
                    Route::get('/content-dwonalod', [GenerateContentsController::class, 'downalodBlogContent'])->name('templates.downalodBlogContent');
                    Route::get('/{template_code}', [TemplatesController::class, 'show'])->name('templates.show');
                });

                # custom templates routes
                Route::group(['prefix' => 'custom', 'middleware' => ['auth']], function () {
                    # custom template categories
                    Route::get('/template-categories', [CustomTemplateCategoriesController::class, 'index'])->name('custom.templateCategories.index');
                    Route::post('/template-categories', [CustomTemplateCategoriesController::class, 'store'])->name('custom.templateCategories.store')->middleware('demo');
                    Route::get('/template-categories/edit/{id}', [CustomTemplateCategoriesController::class, 'edit'])->name('custom.templateCategories.edit');
                    Route::post('/template-categories/update-tag', [CustomTemplateCategoriesController::class, 'update'])->name('custom.templateCategories.update')->middleware('demo');
                    Route::get('/template-categories/delete/{id}', [CustomTemplateCategoriesController::class, 'delete'])->name('custom.templateCategories.delete')->middleware('demo');

                    # custom templates 
                    Route::get('/templates', [CustomTemplatesController::class, 'index'])->name('custom.templates.index');
                    Route::get('/create-template', [CustomTemplatesController::class, 'create'])->name('custom.templates.create');
                    Route::post('/templates', [CustomTemplatesController::class, 'store'])->name('custom.templates.store')->middleware('demo');
                    Route::get('/templates/edit/{id}', [CustomTemplatesController::class, 'edit'])->name('custom.templates.edit');
                    Route::post('/templates/update-tag', [CustomTemplatesController::class, 'update'])->name('custom.templates.update')->middleware('demo');
                    Route::get('/templates/delete/{id}', [CustomTemplatesController::class, 'delete'])->name('custom.templates.delete')->middleware('demo');

                    # generate contents
                    Route::post('/generate', [GenerateContentsController::class, 'generateCustom'])->name('custom.templates.generate');
                    Route::get('/{template_code}', [TemplatesController::class, 'showCustom'])->name('custom.templates.show');
                });

                # chat
                Route::get('/ai-chat', [AiChatController::class, 'index'])->name('chat.index');
                Route::post('/ai-chat', [AiChatController::class, 'store'])->name('chat.store');
                Route::post('/ai-chat/update', [AiChatController::class, 'update'])->name('chat.update');
                Route::get('/ai-chat/delete/{id}', [AiChatController::class, 'delete'])->name('chat.delete');
                Route::post('/ai-chat/new-message', [AIChatController::class, 'newMessage'])->name('chat.newMessage');
                Route::get('/ai-chat/new-message-process', [AIChatController::class, 'process'])->name('chat.process');
                Route::post('/ai-chat/update-balace', [AIChatController::class, 'updateBalanceStopGeneration'])->name('chat.update_balace');
                Route::post('/ai-chat/get-conversations', [AiChatController::class, 'getConversations'])->name('chat.getConversations');
                Route::post('/ai-chat/get-messages', [AiChatController::class, 'getMessages'])->name('chat.getMessages');
                Route::post('/ai-chat/send-email', [AiChatController::class, 'sendInEmail'])->name('chat.sendInEmail');
                Route::get('/ai-chat/download', [AiChatController::class, 'downloadChatHistory'])->name('chat.download');


                # chat experts
                Route::get('/ai-chat/experts', [AiChatExpertController::class, 'index'])->name('chat.experts');
                Route::get('/ai-chat/experts/add-expert', [AiChatExpertController::class, 'create'])->name('chat.createExpert');
                Route::post('/ai-chat/experts/add-expert', [AiChatExpertController::class, 'store'])->name('chat.storeExpert')->middleware('demo');
                Route::get('/ai-chat/experts/edit-expert/{slug}', [AiChatExpertController::class, 'edit'])->name('chat.editExpert');
                Route::post('/ai-chat/experts/update-expert', [AiChatExpertController::class, 'update'])->name('chat.updateExpert')->middleware('demo');
                Route::post('/ai-chat/experts/update-expert-status', [AiChatExpertController::class, 'updateStatus'])->name('chat.updateStatus');

                # chat prompt groups  
                Route::post('/ai-chat/prompts/add-group', [AiChatPromptsController::class, 'storePromptGroup'])->name('chat.storePromptGroup')->middleware('demo');
                Route::post('/ai-chat/prompts/edit-group', [AiChatPromptsController::class, 'editPromptGroup'])->name('chat.editPromptGroup');
                Route::post('/ai-chat/prompts/update-group', [AiChatPromptsController::class, 'updatePromptGroup'])->name('chat.updatePromptGroup')->middleware('demo');
                Route::get('/ai-chat/prompts/delete-group/{id}', [AiChatPromptsController::class, 'deletePromptGroup'])->name('chat.deletePromptGroup');

                # chat prompts
                Route::get('/ai-chat/prompts', [AiChatPromptsController::class, 'index'])->name('chat.prompts'); 
                Route::get('/ai-chat/prompts/add-prompt', [AiChatPromptsController::class, 'create'])->name('chat.createPrompt');
                Route::post('/ai-chat/prompts/add-prompt', [AiChatPromptsController::class, 'store'])->name('chat.storePrompt')->middleware('demo');
                Route::get('/ai-chat/prompts/edit-prompt/{id}', [AiChatPromptsController::class, 'edit'])->name('chat.editPrompt');
                Route::post('/ai-chat/prompts/update-prompt', [AiChatPromptsController::class, 'update'])->name('chat.updatePrompt')->middleware('demo');
                Route::get('/ai-chat/prompts/delete-prompt/{id}', [AiChatPromptsController::class, 'delete'])->name('chat.deletePrompt');

                # blog wizard
                Route::get('/ai-blog-wizard/articles', [BlogWizardController::class, 'index'])->name('blog.wizard'); 
                Route::get('/ai-blog-wizard/create-new-article', [BlogWizardController::class, 'create'])->name('blog.wizard.create');
                Route::post('/ai-blog-wizard/generate-keywords', [BlogWizardController::class, 'generateKeywords'])->name('blog.wizard.generateKeywords');
                Route::post('/ai-blog-wizard/generate-titles', [BlogWizardController::class, 'generateTitles'])->name('blog.wizard.generateTitles');
                Route::post('/ai-blog-wizard/generate-images', [BlogWizardController::class, 'generateImages'])->name('blog.wizard.generateImages');
                Route::post('/ai-blog-wizard/generate-outlines', [BlogWizardController::class, 'generateOutlines'])->name('blog.wizard.generateOutlines'); 
                Route::post('/ai-blog-wizard/init-article', [BlogWizardController::class, 'initArticle'])->name('blog.wizard.initArticle');
                Route::get('/ai-blog-wizard/generate-article', [BlogWizardController::class, 'generateArticle'])->name('blog.wizard.generateArticle');
                Route::get('/ai-blog-wizard/delete-article/{uuid}', [BlogWizardController::class, 'delete'])->name('blog.wizard.delete');
                Route::get('/ai-blog-wizard/{uuid}', [BlogWizardController::class, 'show'])->name('blog.wizard.view'); 
                Route::get('/ai-blog-wizard/edit/{uuid}', [BlogWizardController::class, 'edit'])->name('blog.wizard.edit');  
                Route::post('/ai-blog-wizard/update-blog', [BlogWizardController::class, 'update'])->name('blog.wizard.update');                
                Route::post('/ai-blog/update-balace', [BlogWizardController::class, 'updateBalanceStopGeneration'])->name('blog.wizard.update_balace');
                // populate blog wizard data
                Route::post('/blog-wizard/populate-keywords', [BlogWizardController::class, 'populateKeywordsData'])->name('blogWizard.populateKeywordsData'); 
                Route::post('/blog-wizard/populate-titles', [BlogWizardController::class, 'populateTitlesData'])->name('blogWizard.populateTitlesData'); 
                Route::post('/blog-wizard/populate-images', [BlogWizardController::class, 'populateImagesData'])->name('blogWizard.populateImagesData'); 
                Route::post('/blog-wizard/populate-outlines', [BlogWizardController::class, 'populateOutlinesData'])->name('blogWizard.populateOutlinesData'); 
                Route::post('/blog-wizard/populate-article', [BlogWizardController::class, 'populateArticleData'])->name('blogWizard.populateArticleData'); 
                Route::get('/blog-wizard/article-download', [BlogWizardController::class, 'downloadBlog'])->name('blogWizard.article.download'); 
                Route::get('/blog-wizard/article-publish', [BlogWizardController::class, 'publishBlog'])->name('blogWizard.article.publish'); 
                # publisher routes
                Route::get('/publish/wordpress', [WordpressController::class, 'publishBlog'])->name('publisherWordpress.article.publish');
                   
                # AI images
                Route::get('/generate-images', [GenerateImagesController::class, 'index'])->name('images.index');
                Route::post('/generate-images', [GenerateImagesController::class, 'generate'])->name('images.generate');
                Route::get('/delete-image/{id}', [GenerateImagesController::class, 'delete'])->name('images.delete');

                # stable diffusion
                Route::get('/generate-sd-images', [GenerateSdImagesController::class, 'index'])->name('sdImages.index');
                Route::post('/generate-sd-images', [GenerateSdImagesController::class, 'generate'])->name('sdImages.generate');
                Route::get('/delete-sd-image/{id}', [GenerateSdImagesController::class, 'delete'])->name('sdImages.delete');

                # AI code
                Route::get('/generate-code', [GenerateCodesController::class, 'index'])->name('codes.index');
                Route::post('/generate-code', [GenerateCodesController::class, 'generate'])->name('codes.generate');

                # s2t
                Route::get('/speech-to-text', [GenerateS2TController::class, 'index'])->name('s2t.index');
                Route::post('/speech-to-text', [GenerateS2TController::class, 'generate'])->name('s2t.generate');

                # t2s
                Route::get('/text-to-speech', [GenerateT2SController::class, 'index'])->name('t2s.index');
                Route::post('/text-to-speech', [GenerateT2SController::class, 'generate'])->name('t2s.generate');
                Route::get('/text-to-speech/{id}', [GenerateT2SController::class, 'delete'])->name('t2s.generate.delete');
                Route::get('/text-to-speech-azure', [AzureT2SController::class, 'synthesize'])->name('t2s.generate.synthesize');

                # contact us message
                Route::group(['prefix' => 'contacts'], function () {
                    Route::get('/', [ContactUsMessagesController::class, 'index'])->name('admin.queries.index');
                    Route::get('/mark-as-read/{id}', [ContactUsMessagesController::class, 'read'])->name('admin.queries.markRead');
                    Route::get('/delete-queries/{id}/{force?}', [ContactUsMessagesController::class, 'delete'])->name('admin.queries.delete');
                    Route::get('/delete-all-queries', [ContactUsMessagesController::class, 'deleteAll'])->name('admin.queries.deleteAll');
                });

                # openAi settings 
                Route::get('/settings/open-ai', [SettingsController::class, 'openAi'])->name('admin.settings.openAi');
                Route::get('/settings/open-ai-models', [SettingsController::class, 'openAiModeList'])->name('admin.settings.openAi.models');

                # auth settings 
                Route::get('/settings/auth', [SettingsController::class, 'authSettings'])->name('admin.settings.authSettings');

                # otp settings 
                Route::get('/settings/otp', [SettingsController::class, 'otpSettings'])->name('admin.settings.otpSettings');

                # settings
                Route::post('/settings/env-key-update', [SettingsController::class, 'envKeyUpdate'])->name('admin.envKey.update')->middleware('demo');
                Route::get('/settings/general-settings', [SettingsController::class, 'index'])->name('admin.generalSettings');
                Route::get('/settings/smtp-settings', [SettingsController::class, 'smtpSettings'])->name('admin.smtpSettings.index');
                Route::post('/settings/test/smtp', [SettingsController::class, 'testEmail'])->name('admin.test.smtp')->middleware('demo');
                Route::post('/settings/update', [SettingsController::class, 'update'])->name('admin.settings.update')->middleware('demo');

                #payment methods 
                Route::get('/settings/payment-methods', [SettingsController::class, 'paymentMethods'])->name('admin.settings.paymentMethods');
                Route::post('/settings/update-payment-methods', [PaymentGatewayController::class, 'updateGatewayDetails'])->name('admin.settings.updatePaymentMethods')->middleware('demo');

                # social login
                Route::get('/settings/social-media-login', [SettingsController::class, 'socialLogin'])->name('admin.settings.socialLogin');
                Route::post('/settings/activation', [SettingsController::class, 'updateActivation'])->name('admin.settings.activation');

                # currencies  
                Route::get('/settings/currencies', [CurrenciesController::class, 'index'])->name('admin.currencies.index');
                Route::post('/settings/store-currency', [CurrenciesController::class, 'store'])->name('admin.currencies.store')->middleware('demo');
                Route::get('/settings/currencies/edit/{id}', [CurrenciesController::class, 'edit'])->name('admin.currencies.edit');
                Route::post('/settings/update-currency', [CurrenciesController::class, 'update'])->name('admin.currencies.update')->middleware('demo');
                Route::post('/settings/update-currency-status', [CurrenciesController::class, 'updateStatus'])->name('admin.currencies.updateStatus')->middleware('demo');

                # languages  
                Route::get('/settings/languages', [LanguageController::class, 'index'])->name('admin.languages.index');
                Route::post('/settings/store-language', [LanguageController::class, 'store'])->name('admin.languages.store')->middleware('demo');
                Route::get('/settings/languages/edit/{id}', [LanguageController::class, 'edit'])->name('admin.languages.edit');
                Route::post('/settings/update-language', [LanguageController::class, 'update'])->name('admin.languages.update')->middleware('demo');
                Route::post('/settings/update-language-status', [LanguageController::class, 'updateStatus'])->name('admin.languages.updateStatus')->middleware('demo');
                Route::post('/settings/update-language-template-status', [LanguageController::class, 'updateTemplateStatus'])->name('admin.languages.updateTemplateStatus')->middleware('demo');
                Route::post('/settings/update-language-default-status', [LanguageController::class, 'defaultLanguage'])->name('admin.languages.defaultLanguage')->middleware('demo');

                # localizations
                Route::get('/settings/languages/localizations/{id}', [LanguageController::class, 'showLocalizations'])->name('admin.languages.localizations');
                Route::post('/settings/languages/key-value-store', [LanguageController::class, 'key_value_store'])->name('admin.languages.key_value_store')->middleware('demo');

                # pages
                Route::group(['prefix' => 'pages'], function () {
                    Route::get('/', [PagesController::class, 'index'])->name('admin.pages.index');
                    Route::get('/add-page', [PagesController::class, 'create'])->name('admin.pages.create');
                    Route::post('/add-page', [PagesController::class, 'store'])->name('admin.pages.store')->middleware('demo');
                    Route::get('/edit/{id}', [PagesController::class, 'edit'])->name('admin.pages.edit');
                    Route::post('/update-page', [PagesController::class, 'update'])->name('admin.pages.update')->middleware('demo');
                    Route::get('/delete/{id}', [PagesController::class, 'delete'])->name('admin.pages.delete');
                });

                # faqs
                Route::get('/faqs', [FaqsController::class, 'index'])->name('admin.faqs.index');
                Route::post('/new-faq', [FaqsController::class, 'store'])->name('admin.faqs.store')->middleware('demo');
                Route::get('/faqs/edit/{id}', [FaqsController::class, 'edit'])->name('admin.faqs.edit');
                Route::post('/faqs/update-faq', [FaqsController::class, 'update'])->name('admin.faqs.update')->middleware('demo');
                Route::get('/faqs/delete/{id}', [FaqsController::class, 'delete'])->name('admin.faqs.delete')->middleware('demo');

                # testimonials
                Route::group(['prefix' => 'testimonials'], function () {
                    Route::get('/', [ContactUsMessagesController::class, 'index'])->name('admin.testimonials.index');
                    Route::get('/add', [ContactUsMessagesController::class, 'index'])->name('admin.testimonials.edit')->middleware('demo');
                });

                # customers
                Route::group(['prefix' => 'customers', 'middleware' => 'admin'], function () {
                    Route::get('/', [CustomersController::class, 'index'])->name('admin.customers.index');
                    Route::get('/create', [CustomersController::class, 'create'])->name('admin.customers.create');
                    Route::post('/store', [CustomersController::class, 'store'])->name('admin.customers.store')->middleware('demo');
                    Route::get('/edit/{id}', [CustomersController::class, 'edit'])->name('admin.customers.edit');
                    Route::post('/update', [CustomersController::class, 'update'])->name('admin.customers.update')->middleware('demo');
                    Route::get('/delete/{id}', [CustomersController::class, 'delete'])->name('admin.customers.delete')->middleware('demo');
                    Route::get('/assign-package/{id}', [CustomersController::class, 'assignPackage'])->name('admin.customers.assignPackage');
                    Route::post('/assign-package', [CustomersController::class, 'assignPackageStore'])->name('admin.customers.assignPackageStore')->middleware('demo');
                    Route::post('/update-banned-customer', [CustomersController::class, 'updateBanStatus'])->name('admin.customers.updateBanStatus')->middleware('demo');
                });

                # tags
                Route::get('/tags', [TagsController::class, 'index'])->name('admin.tags.index');
                Route::post('/tag', [TagsController::class, 'store'])->name('admin.tags.store')->middleware('demo');
                Route::get('/tags/edit/{id}', [TagsController::class, 'edit'])->name('admin.tags.edit');
                Route::post('/tags/update-tag', [TagsController::class, 'update'])->name('admin.tags.update')->middleware('demo');
                Route::get('/tags/delete/{id}', [TagsController::class, 'delete'])->name('admin.tags.delete')->middleware('demo');

                # blog system
                Route::group(['prefix' => 'blogs'], function () {
                    # blogs
                    Route::get('/', [BlogsController::class, 'index'])->name('admin.blogs.index');
                    Route::get('/add-blog', [BlogsController::class, 'create'])->name('admin.blogs.create');
                    Route::post('/add-blog', [BlogsController::class, 'store'])->name('admin.blogs.store')->middleware('demo');
                    Route::get('/edit/{id}', [BlogsController::class, 'edit'])->name('admin.blogs.edit');
                    Route::post('/update-blog', [BlogsController::class, 'update'])->name('admin.blogs.update')->middleware('demo');
                    Route::post('/update-popular', [BlogsController::class, 'updatePopular'])->name('admin.blogs.updatePopular');
                    Route::post('/update-status', [BlogsController::class, 'updateStatus'])->name('admin.blogs.updateStatus');
                    Route::get('/delete/{id}', [BlogsController::class, 'delete'])->name('admin.blogs.delete')->middleware('demo');

                    # categories
                    Route::get('/categories', [BlogCategoriesController::class, 'index'])->name('admin.blogCategories.index');
                    Route::post('/category', [BlogCategoriesController::class, 'store'])->name('admin.blogCategories.store')->middleware('demo');
                    Route::get('/categories/edit/{id}', [BlogCategoriesController::class, 'edit'])->name('admin.blogCategories.edit');
                    Route::post('/categories/update-category', [BlogCategoriesController::class, 'update'])->name('admin.blogCategories.update')->middleware('demo');
                    Route::get('/categories/delete/{id}', [BlogCategoriesController::class, 'delete'])->name('admin.blogCategories.delete')->middleware('demo');
                });

                # media manager 
                Route::get('/media-manager', [MediaManagerController::class, 'index'])->name('admin.mediaManager.index');

                # bulk-emails
                Route::controller(NewslettersController::class)->group(function () {
                    Route::get('/bulk-emails', 'index')->name('admin.newsletters.index');
                    Route::post('/bulk-emails/send', 'sendNewsletter')->name('admin.newsletters.send')->middleware('demo');
                });

                # subscribed users
                Route::get('/subscribers', [SubscribersController::class, 'index'])->name('admin.subscribers.index');
                Route::get('/subscribers/delete/{id}', [SubscribersController::class, 'delete'])->name('admin.subscribers.delete')->middleware('demo');

                # roles & permissions
                Route::group(['prefix' => 'roles'], function () {
                    Route::get('/', [RolesController::class, 'index'])->name('admin.roles.index');
                    Route::get('/add-role', [RolesController::class, 'create'])->name('admin.roles.create');
                    Route::post('/add-role', [RolesController::class, 'store'])->name('admin.roles.store')->middleware('demo');
                    Route::get('/update-role/{id}', [RolesController::class, 'edit'])->name('admin.roles.edit');
                    Route::post('/update-role', [RolesController::class, 'update'])->name('admin.roles.update')->middleware('demo');
                    Route::get('/delete-role/{id}', [RolesController::class, 'delete'])->name('admin.roles.delete')->middleware('demo');
                });

                # appearance
                Route::group(['prefix' => 'appearance'], function () {
                    # homepage - hero
                    Route::get('/homepage/hero', [HomepageController::class, 'hero'])->name('admin.appearance.homepage.hero');

                    # homepage - trustedBy
                    Route::get('/homepage/trusted-by', [HomepageController::class, 'trustedBy'])->name('admin.appearance.homepage.trustedBy');

                    # homepage - howItWorks
                    Route::get('/homepage/how-it-works', [HomepageController::class, 'howItWorks'])->name('admin.appearance.homepage.howItWorks');

                    # homepage - featureImages
                    Route::get('/homepage/feature-images', [HomepageController::class, 'featureImages'])->name('admin.appearance.homepage.featureImages');

                    # homepage - cta
                    Route::get('/homepage/cta', [HomepageController::class, 'cta'])->name('admin.appearance.homepage.cta');

                    # client feedback
                    Route::get('/homepage/client-feedback', [ClientFeedbackController::class, 'index'])->name('admin.appearance.homepage.clientFeedback');
                    Route::post('/homepage/client-feedback', [ClientFeedbackController::class, 'store'])->name('admin.appearance.homepage.storeClientFeedback')->middleware('demo');
                    Route::get('/homepage/client-feedback/edit/{id}', [ClientFeedbackController::class, 'edit'])->name('admin.appearance.homepage.editClientFeedback');
                    Route::post('/homepage/client-feedback/update', [ClientFeedbackController::class, 'update'])->name('admin.appearance.homepage.updateClientFeedback')->middleware('demo');
                    Route::get('/homepage/client-feedback/delete/{id}', [ClientFeedbackController::class, 'delete'])->name('admin.appearance.homepage.deleteClientFeedback');

                    # about us page
                    Route::get('/about-us', [AboutUsController::class, 'index'])->name('admin.appearance.aboutUs');

                    # header
                    Route::get('/header', [HeaderController::class, 'index'])->name('admin.appearance.header');

                    # footer
                    Route::get('/footer', [FooterController::class, 'index'])->name('admin.appearance.footer');
                });

                # staffs
                Route::group(['prefix' => 'staffs'], function () {
                    Route::get('/', [StaffsController::class, 'index'])->name('admin.staffs.index');
                    Route::get('/add-staff', [StaffsController::class, 'create'])->name('admin.staffs.create');
                    Route::post('/add-staff', [StaffsController::class, 'store'])->name('admin.staffs.store')->middleware('demo');
                    Route::get('/update-staff/{id}', [StaffsController::class, 'edit'])->name('admin.staffs.edit');
                    Route::post('/update-staff', [StaffsController::class, 'update'])->name('admin.staffs.update')->middleware('demo');
                    Route::get('/delete-staff/{id}', [StaffsController::class, 'delete'])->name('admin.staffs.delete')->middleware('demo');
                });


                # reports
                Route::group(['prefix' => 'reports'], function () {
                    Route::get('/words-generated', [ReportsController::class, 'words'])->name('admin.reports.words');
                    Route::get('/codes-generated', [ReportsController::class, 'codes'])->name('admin.reports.codes');
                    Route::get('/images-generated', [ReportsController::class, 'images'])->name('admin.reports.images');
                    Route::get('/speech-to-text-generated', [ReportsController::class, 's2t'])->name('admin.reports.s2t');
                    Route::get('/most-used-templates', [ReportsController::class, 'mostUsed'])->name('admin.reports.mostUsed');
                    Route::get('/subscriptions', [ReportsController::class, 'subscriptions'])->name('admin.reports.subscriptions');
                });

                # Multi Open Ai 
                Route::group(['prefix' => 'settings/multi-open-ai'], function () {
                    Route::get('/', [MultiOpenAiController::class, 'index'])->name('admin.multiOpenAi.index');
                    Route::get('/model-list/{id}', [MultiOpenAiController::class, 'openAiModeList'])->name('admin.multiOpenAi.models');
                    Route::get('/add-open-ai-key', [MultiOpenAiController::class, 'create'])->name('admin.multiOpenAi.create');
                    Route::post('/add-open-ai-key', [MultiOpenAiController::class, 'store'])->name('admin.multiOpenAi.store')->middleware('demo');
                    Route::get('/update-open-ai-key/{id}', [MultiOpenAiController::class, 'edit'])->name('admin.multiOpenAi.edit');
                    Route::post('/update-open-ai-key', [MultiOpenAiController::class, 'update'])->name('admin.multiOpenAi.update')->middleware('demo');
                    Route::get('/delete-open-ai-key/{id}', [MultiOpenAiController::class, 'delete'])->name('admin.multiOpenAi.delete');
                    Route::post('/update-open-ai-status', [MultiOpenAiController::class, 'updateStatus'])->name('admin.multiOpenAi.updateStatus');
                });

                # offline Payment Method
                Route::group(['prefix' => 'settings/offline-payment'], function () {
                    Route::get('/', [OfflinePaymentMethodController::class, 'index'])->name('admin.offline-payment.index');
                    Route::post('/', [OfflinePaymentMethodController::class, 'store'])->name('admin.offline-payment.store');
                    Route::get('/edit/{id}', [OfflinePaymentMethodController::class, 'edit'])->name('admin.offline-payment.edit');
                    Route::post('/update', [OfflinePaymentMethodController::class, 'update'])->name('admin.offline-payment.update');
                    Route::get('/delete/{id}', [OfflinePaymentMethodController::class, 'delete'])->name('admin.offline-payment.delete');
                    Route::post('/update-status', [OfflinePaymentMethodController::class, 'updateStatus'])->name('admin.offline-payment.updateStatus');
                });

                # payment Request
                Route::group(['prefix' => 'payment-request', 'as' => 'admin.payment-request.'], function ($routes) {
                    $routes->get('/', [PaymentRequestController::class, 'index'])->name('index');
                    $routes->get('/view/{id}', [PaymentRequestController::class, 'view'])->name('view');
                    $routes->get('/approve/{id}', [PaymentRequestController::class, 'approve'])->name('approve');
                    $routes->get('/reject/{id}', [PaymentRequestController::class, 'reject'])->name('reject');
                    $routes->get('/delete/{id}', [PaymentRequestController::class, 'delete'])->name('delete');
                    $routes->get('/re-submit/{id}', [PaymentRequestController::class, 'reSubmit'])->name('re-submit');
                    $routes->post('/feedback-note', [PaymentRequestController::class, 'feedbackNote'])->name('feedback-note');
                });
                # customer re-submit Request
                Route::group(['prefix' => 're-submit-request', 'as' => 'admin.re-submit-request.'], function ($routes) {
                    $routes->get('/{id}', [CustomerReSubmitRequestController::class, 'index'])->name('index');
                    $routes->post('/', [CustomerReSubmitRequestController::class, 'store'])->name('store');
                });
                # Subscription settings
                Route::group(['prefix' => 'subscription-settings', 'as' => 'admin.subscription-settings.'], function ($routes) {
                    $routes->get('/', [SubscriptionSettingsController::class, 'index'])->name('index');
                    $routes->post('/', [SubscriptionSettingsController::class, 'store'])->name('store');
                    $routes->post('/store-gateway-product', [SubscriptionSettingsController::class, 'storeGatewayproduct'])->name('store.gateway.product');
                    $routes->get('/gateway-product/view/{id}', [SubscriptionSettingsController::class, 'view'])->name('view.gateway.product');
                    $routes->get('/gateway-product/delete/{id}', [SubscriptionSettingsController::class, 'delete'])->name('delete.gateway.product');
                });
                # Customer settings
                Route::group(['prefix' => 'customer-settings', 'as' => 'customer.settings.'], function ($routes) {
                    $routes->get('/', [CustomerSettingsController::class, 'index'])->name('index');
                    $routes->post('/', [CustomerSettingsController::class, 'store'])->name('store');
                });

                # google tts settings
                Route::get('/settings/voice-settings', [VoiceSettingController::class, 'index'])->name('admin.settings.voice-settings');
                Route::post('/settings/voice-settings', [VoiceSettingController::class, 'update'])->name('admin.settings.voice-settings.update');
                Route::post('/settings/enable-default-voiceover', [VoiceSettingController::class, 'defaultVoiceOver'])->name('admin.settings.voice-settings.enable');
                #role status update
                Route::post('/role/status-update', [RolesController::class, 'updateStatus'])->name('admin.role.updateStatus');
                Route::post('update-status', [ConstantController::class, 'updateStatus'])->name('admin.update-status');

                #active package
                Route::post('customer-active-package', [CustomersController::class, 'activePackage'])->name('customer.package.active');

                #cron jon list
                Route::get('/settings/cron-job-list', [SettingsController::class, 'cronJobList'])->name('admin.cronJobList');

                # storage management
                 Route::group(['prefix' => 'storage-management', 'as'=>'admin.storage-management.'], function ($routes) {
                    $routes->get('/', [FileStorageManagerController::class, 'index'])->name('index');                 
                    $routes->post('/update', [FileStorageManagerController::class, 'update'])->name('update')->middleware('demo');
                    $routes->post('/active-storage', [FileStorageManagerController::class, 'activeStorage'])->name('active-storage')->middleware('demo');
                  
                });

                 #update system
                 Route::get('/settings/update-system', [UpdateController::class, 'about'])->name('admin.about-update');
                 Route::post('/settings/update-system', [UpdateController::class, 'versionUpdateInstall'])->name('admin.system.update-version');
                 Route::get('/utilities', [UtilityController::class, 'index'])->name('admin.utilities');
                 Route::get('/clear-cache', [UtilityController::class, 'clearCache'])->name('admin.clear-cache');
                 Route::get('/clear-log', [UtilityController::class, 'clearLog'])->name('admin.clearLog');
                 Route::get('/debug', [UtilityController::class, 'debug'])->name('admin.debug');

                # system noitifaction
                Route::group(['prefix' => 'notification'], function () {
                    Route::get('/', [WrNotificationContoller::class, 'index'])->name('admin.notifications.index');
                    Route::get('/mark-as-read/{id}', [WrNotificationContoller::class, 'read'])->name('admin.notifications.markRead');             
                    Route::get('/delete-notification', [WrNotificationContoller::class, 'deleteAll'])->name('admin.notifications.delete');
                    Route::get('/delete-all-notifications', [WrNotificationContoller::class, 'deleteAll'])->name('admin.notifications.deleteAll');
                });
                Route::get('/settings/update', [SystemUpdateController::class, 'index'])->name('system.update');
                Route::get('/settings/file-permission', [FilePermissionController::class, 'index'])->name('system.file-permission');
               
            }
        );
    }
);
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('/paypal', [PaypalController::class, 'handleWebhook']);
});