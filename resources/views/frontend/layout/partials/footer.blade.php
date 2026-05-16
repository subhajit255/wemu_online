<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-12">
                <div class="ft-logo">
                    <a href="#">
                        <img src="{{ asset('assets/frontend') }}/images/ft-logo.png" alt="">
                    </a>
                </div>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-12">
                <div class="row justify-content-end">
                    <div class="col-lg-3 col-md-4 col-sm-12">
                        {{-- <div class="ft-box">
                            <h3>Quick Links</h3>
                            <ul class="ftmenu">
                                <li><a target="blank" href="{{ route('term.and.conditions') }}">Term & conditions</a>
                                </li>
                                <li><a target="blank" href="{{ route('privacy.policy') }}">Privacy policy</a></li>
                            </ul>
                        </div> --}}
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <div class="ft-box">
                            <h3>Quick Links</h3>
                            <ul class="ftmenu">
                                <li><a target="blank" href="{{ route('term.and.conditions') }}">Term & conditions</a>
                                </li>
                                <li><a target="blank" href="{{ route('privacy.policy') }}">Privacy policy</a></li>
                            </ul>
                        </div>
                    </div>
                    @if ($settings && $settings->show_social_media)
                        <div class="col-lg-3 col-md-4 col-sm-12">
                            <div class="ft-box">
                                <h3>FOLLOW US</h3>
                                <ul class="ftsocial">
                                    <li><a target="_blank" href="{{ $settings->facebook }}"><i
                                                class="fa-brands fa-square-facebook"></i></a></li>
                                    <li><a target="_blank" href="{{ $settings->instagram }}"><i
                                                class="fa-brands fa-square-instagram"></i></a></li>
                                    <li><a target="_blank" href="{{ $settings->twitter }}"><i
                                                class="fa-brands fa-square-twitter"></i></a></li>
                                    <li><a target="_blank" href="{{ $settings->linkedin }}"><i
                                                class="fa-brands fa-linkedin"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    @endif
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="copyRight">
                            <p>© {{ date('Y') }} Copyrights. Keep Your Ducks In A Row Pty Ltd. All rights reserved.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
