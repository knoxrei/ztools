<?php

use App\Http\Controllers\DownloaderController;
use App\Http\Controllers\ShortLinkRedirectController;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::welcome')->name('home');
Route::livewire('/enc', 'pages::tools.encryption')->name('encryption');
Route::livewire('/dec', 'pages::tools.decryption')->name('decryption');
Route::livewire('/hash', 'pages::tools.hash-checksum')->name('hash-checksum');
Route::livewire('/forensics', 'pages::tools.forensics')->name('forensics');
Route::livewire('/qrcode', 'pages::tools.generator.qrcode')->name('qrcode');
Route::livewire('/fake-identity', 'pages::tools.generator.fake-identity')->name('fake-identity');
Route::livewire('/shortlink', 'pages::tools.shortlink')->name('shortlink');
Route::livewire('/tools', 'pages::tools.index')->name('tools');
Route::livewire('/support', 'pages::support')->name('support');
Route::livewire('/url-checker', 'pages::tools.url-checker')->name('url-checker');
Route::livewire('/filehost', 'pages::tools.filehost')->name('filehost');
Route::livewire('/services', 'pages::tools.services')->name('services');
Route::livewire('/downloader', 'pages::tools.downloader')->name('downloader');
Route::post('/downloader/fetch', [DownloaderController::class, 'fetch'])->name('downloader.fetch');
Route::get('/downloader/thumb', [DownloaderController::class, 'thumb'])->name('downloader.thumb');
Route::livewire('/contact', 'pages::contact')->name('contact');

Route::get('/s/{code}', [ShortLinkRedirectController::class, 'handle'])->name('short-link.redirect');
