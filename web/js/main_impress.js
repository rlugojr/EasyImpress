(function ($, d, w) {

    function holderJS (content, add){
        var html;
        if (!this.nodeToChange) {
            this.nodeToChange = d.getElementById('holderjs-style');
        }
        content = '/**@**/'+content+'/**@**/';
        html = this.nodeToChange.innerHTML.replace(content, '');
        if (add === true) {
            html += content;
        }
        this.nodeToChange.innerHTML = html;
        return this.nodeToChange;
    }

    $('.fallback-message').hide();

    window.addEventListener('impress:stepenter', function (event) {
        for (var i= 0, a = d.querySelectorAll('.step.leaving'), l = a.length; i < l; i++) {
            a[i].classList.remove('leaving');
        }
        $('#slider-thumbnails a.active').filter(function(e){return e.id !== 'link_'+event.target.id;}).removeClass('active');
        if (d.getElementById('link_'+event.target.id)) {
            d.getElementById('link_'+event.target.id).classList.add('active');
        }
        if (event.target.id === 'overview') {
            d.getElementById('impress').classList.add('overview');
        } else {
            d.getElementById('impress').classList.remove('overview');
        }
    });
    window.addEventListener('impress:stepleave', function (event) {
        event.target.classList.add('leaving');
    });

    var impressElement = impress();
    impressElement.init();

    $('#left_arrow_clickable').on('click',function(){impressElement.prev();});
    $('#right_arrow_clickable').on('click',function(){impressElement.next();});

    $('.credit-collapse').click(function(){
        var $this = $(this);
        $this.next('.credits-inner').toggleClass('active').stop().slideToggle(200);
        if ($this.hasClass('glyphicon-chevron-up')) {
            $this.removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        } else {
            $this.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        }
    });

    var $nav = $('#navigation');
    var $thumbnav = $('#slider-thumbnails');
    var t;

    function resizeTitleWithHolder(){
        var cssContentLarge =
                '#impress .step h2 { margin-top: 80px;}' +
                '#impress .step[data-image="true"]{' +
                    'height: -webkit-calc(100% - 152px);' +
                    'height: -moz-calc(100% - 152px);' +
                    'height: calc(100% - 152px);' +
                'margin-top: 76px;}',

            cssContentMedium = '#impress .step h2 { margin-top: 30px;}' +
                '#impress .step[data-image="true"]{' +
                'height: -webkit-calc(100% - 102px);' +
                'height: -moz-calc(100% - 102px);' +
                'height: calc(100% - 102px);' +
                'margin-top: 51px;}',

            cssContentCreditsMedium = '#impress .step .credits {bottom: 190px;}',
            navHeight = $nav.height(),
            thumbNavHeight = $thumbnav.height()
        ;
        holderJS(cssContentMedium);
        holderJS(cssContentLarge);
        holderJS(cssContentCreditsMedium);

        if (!d.getElementById('slide-home-coming-soon')) {
            if (navHeight >= 140) {
                holderJS(cssContentLarge, true);
            } else if (navHeight >= 100) {
                holderJS(cssContentMedium, true);
            }

            if (thumbNavHeight >= 100) {
                holderJS(cssContentCreditsMedium, true);
            }
        }

    }

    $(w).resize(function() {
        if (t) {
            clearTimeout(t);
        }
        t = setTimeout(resizeTitleWithHolder, 20);
    });

    resizeTitleWithHolder();

})(jQuery, document, window);