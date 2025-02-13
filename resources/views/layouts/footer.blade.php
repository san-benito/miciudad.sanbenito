<div id="footer">
  <div class="container">
    <div class="row text-smaller text-center text-lg-left">
      <div class="col-lg-4 mb-2 mb-lg-0">
        <img src="{{asset(app_setting('app_logo_footer','img/default-logo-color.svg'))}}" class="footer-logo" alt="">
        <p class="">
        {!! nl2br(e(app_setting('app_footer_description'))) !!}
        </p>
      </div>
      <div class="col-lg-4 mb-2 mb-lg-0">
        <p class="mb-1 mb-lg-2"><b>Sobre partícipes</b></p>
        <p class="mb-1"><a href="{{route('about.general')}}">Acerca de Participes</a></p>
        <p class="mb-1"><a href="{{route('about.faq')}}">Preguntas frecuentes</a></p>
        <p class="mb-1"><a href="{{route('about.legal')}}#términos">Legales</a></p>
      </div>
      <div class="col-lg-4 mb-2 mb-lg-0">
        <p class="mb-lg-2"><b>Contactenos</b></p>
        <p>{!! nl2br(e(app_setting('app_footer_contact_info'))) !!}</p>
      </div>
      {{-- <div class="col-lg-2 mb-0">
        <a href="https://democraciaenred.org" target="_blank"><img src="{{asset('img/der-black.svg')}}" class="footer-logo" alt="Democracia en Red"></a>
        <br>Desarrollado con <i class="far fa-heart text-danger"></i> por Democracia en Red
      </div> --}}
    </div>
  </div>
</div>
{{--
<div class="bg-light">
  <div id="post-footer" class="container">
    <div class="row py-3 text-center">
      <div class="col-12">

        <img src="{{asset('img/der-black.svg')}}" class="footer-logo" alt="Democracia en Red">
        <img src="{{asset('img/eu-flag.svg')}}" class="footer-logo-eu" alt="Democracia en Red">
      <br>
      <p>
      Desarrollado por Democracia en Red con el apoyo de la Unión Europea
      </p>
      </div>
    </div>
  </div>
</div>
--}}