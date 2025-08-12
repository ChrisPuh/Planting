
@props(['viewModel'])

@if($viewModel->hasTitle())
    <flux:separator :text="$viewModel->getTitle()"/>
@endif

@php
    // Setze die richtige Variable für das Partial
    ${$viewModel->getVariableName()} = $viewModel;
@endphp

@include($viewModel->getPartial())
