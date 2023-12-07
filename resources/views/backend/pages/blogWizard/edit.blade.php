@extends('backend.layouts.master')

@section('title')
    {{ localize('Update Blog Details') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection

@section('contents')
    <section class="tt-section pt-4">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="tt-page-header">
                        <div class="d-lg-flex align-items-center justify-content-lg-between">
                            <div class="tt-page-title mb-3 mb-lg-0">
                                <h1 class="h4 mb-lg-1">{{ localize('Update Blog Details') }}</h1>
                                <ol class="breadcrumb breadcrumb-angle text-muted">
                                    <li class="breadcrumb-item"><a
                                            href="{{ route('writebot.dashboard') }}">{{ localize('Dashboard') }}</a>
                                    </li>
                                    <li class="breadcrumb-item"><a
                                            href="{{ route('blog.wizard') }}">{{ localize('Ai Blog Articles') }}</a>
                                    </li>
                                    <li class="breadcrumb-item">{{ localize('Update Blog Details') }}</li>
                                </ol>
                            </div>
                            <div class="tt-action">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 col-lg-8 mx-auto">
                    <form action="" method="POST" class="card card-body content-form-blog-wizard">
                        @csrf
                        <input class="ai_blog_wizard_article_id" type="hidden" name="ai_blog_wizard_article_id"
                            value="{{ $article->id }}">

                        <div class="row justify-content-between align-items-center g-2 p-3">
                            <div class="col-auto flex-grow-1">
                                <input class="form-control border-0 px-0 project-title" type="text" id="title"
                                    name="title" placeholder="{{ localize('Your blog title') }}..." @
                                    value="{{ $article->title }}">
                            </div>

                            <div class="col-auto dropdown tt-tb-dropdown">
                                <button type="button"
                                    class="btn tt-icon-btn tt-icon-primary border-0 shadow-sm rounded-circle p-0 me-2"
                                    data-bs-title="Download" id="downloadDropdown" href="#!" role="button"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                                    aria-expanded="true"><i data-feather="download"></i></button>

                                <div class="dropdown-menu dropdown-menu-end shadow" style="">

                                    <a href="{{route('blogWizard.article.download', ['id'=>$article->id, 'type'=>'pdf'])}}" target="_blank" class="dropdown-item downloadBtn" data-doc-type="pdf"
                                        data-doc-name="{{ isset($article) ? $article->title : '' }}">
                                        <i data-feather="file-text" class="icon-18"></i> {{ localize('PDF') }}
                                    </a>

                                    <a href="{{route('blogWizard.article.download', ['id'=>$article->id, 'type'=>'html'])}}" class="dropdown-item downloadBtn" data-doc-type="html"
                                        data-doc-name="{{ isset($article) ? $article->title : '' }}">
                                        <svg class="shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 4l-2 14.5l-6 2l-6 -2l-2 -14.5z"></path>
                                            <path d="M15.5 8h-7l.5 4h6l-.5 3.5l-2.5 .75l-2.5 -.75l-.1 -.5"></path>
                                        </svg> {{ localize('HTML') }}
                                    </a>

                                    <a href="{{route('blogWizard.article.download', ['id'=>$article->id, 'type'=>'word'])}}" class="dropdown-item downloadBtn" data-doc-type="doc"
                                        data-doc-name="{{ isset($article) ? $article->title : '' }}">
                                        <svg class="shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 18h9v-12l-5 2v5l-4 2v-8l9 -4l7 2v13l-7 3z"></path>
                                        </svg> {{ localize('MS Word') }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-auto">
                            <a href="{{route('blogWizard.article.publish', ['id'=>$article->id])}}"><button type="button"
                            class="tt-icon-btn tt-icon-warning border-0 shadow-sm rounded-circle publishBtn"
                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{localize('Publish Content')}}"><i
                                data-feather="calendar"></i></button></a>
                    

                            </div>

                            <div class="col-auto">
                                <button type="button"
                                    class="tt-icon-btn tt-icon-warning border-0 shadow-sm rounded-circle copyBtn"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{localize('Copy Contents')}}"><i
                                        data-feather="copy"></i></button>
                            </div>

                            <div class="col-auto">
                                <button type="submit"
                                    class="tt-icon-btn tt-icon-success border-0 shadow-sm rounded-circle content-form-submit"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{localize('Save Changes')}}"><i
                                        data-feather="save"></i></button>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column h-100 tt-create-content-wrap p-0 border-top">
                            <textarea class="editor content-editor" data-content-min-height="true" class="p-0 border-0" id="aiContents"
                                name="article">
                        @if ($article->image != null && $article->updated_by == null)
                            <img src="{{ staticAsset($article->image) }}" alt="" class="img-fluid tt-blog-img-wrap rounded mb-4">
                        @endif
                        @php
                            $result = $article->value;
                        @endphp
                        <div class="article-content">
                            {!! preg_replace('/\*\*(.*?)\*\*/', '<h3 class="mb-0 mt-4 h5">$1</h3>', $result) !!} 
                            </div>
                    </textarea>
                        </div>
                    </form>

                </div>
            </div>


        </div>
    </section>
@endsection

@section('scripts')
    @include('backend.pages.blogWizard.inc.initScripts')
    @include('backend.pages.blogWizard.inc.scripts')
    <script>
        setTimeout(() => {
            populateArticleData({{ $article->id }})
        }, 300);
    </script>
@endsection
