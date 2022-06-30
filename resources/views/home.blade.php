@section('title')
  SIAL - YLB
@endsection

@extends('layouts.app')

@section('content')
  <div id="panel-wrapper">
    <div id="app">
      <v-app>
        <sidenav logo="{{ asset('images/logo-color.png') }}"></sidenav>
        <div id="content">
          <topnav></topnav>
          <transition name="router-anim" mode="out-in">
            <router-view :key="$route.path">
            </router-view>
          </transition>
        </div> {{--content--}}
      </v-app>
    </div> {{--app--}}
  </div>{{--panel wrapper--}}
@endsection
