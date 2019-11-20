<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 19.04.2019
 * Time: 14:27
 */

return [
    'valid' => [
        "https://localhost/",		// If need - just add to regex
        "http://test?123pass@localhost", // different PHP versions parse differently
        "http://foo.com?@bar.com/", // different PHP versions parse differently TODO my parser not correct??

        "http://example/asd", // it can be valid at private networks (with specific DNS resolving), but not for global internet
        "http://example//asd",

        'http://example.com/\action.php', //valid
        "http://example.com/экшн.пхп",
        "http://example.com/action.php?",
        "http://example.com/action.php?token",
        "http://www.example.com/wpstyle/?p=364",
        "http://example.com/?r=http://example.com/2",
        "http://example.com/~#!@$%^&()_-=+*'[x][y];,.", // with anchor (fragment)
        "http://example.com/~!@$&()_-=+*';,.action.php",

        "https://example.c/short-tld", // RFC allow, all - https://data.iana.org/TLD/tlds-alpha-by-domain.txt
//        "example.com/action.php",	// not valid URI if relative not desired
        "http://example.com.",		// Technically valid
        "http://www.example.com./",
        "http://example./com",
        "http://examle.co/asd#qwe#m",
        "http://examle.co#m",

        "//example.com", // absolute url with relative scheme (network path reference)
        "//a",
        "//test/asd",

        "https://666.com/",
        "http://google.com:80/",
        "http://google3.com",

        "http://google?.com",   // query with empty path

        "http://foo.com/blah_blah/",
        "http://foo.com/blah_blah_(wikipedia)_(again)",
        "http://foo.com/blah_(wikipedia)_blah#cite-1",

        "http://foo.com/(something)?after=parens",
        "http://foo.bar/?q=Test%20URL-encoded%20stuff",
        "http://ABC.com/%7Esmith/home.html",

        "http://a.b-c.de",
        "http://a.b.c.d.e.f.g.h.i.j.k.l.m.n.o.p.q.r.s.t.u.v.w.x.y.z.com",
        "http://code.google.com/events/#&product=browser",
        "http://j.mp",
        "http://foo-bar.com/baz/quo/",

//        "http://www.example.com/%CE%B8%",
//        "http://use%2G@example.com", // incorrect percent-encoded, but valid
//        "http://user:ö@example.com",

        "http://mw1.google.com/mw-earth-vectordb/kml-samples/gp/seattle/gigapxl/$[level]/r$[y]_c$[x].jpg", // [] - brackets gen-delims not allowed in theory, but workable
        "https://example.com/?a^=b", // valid

        "http://user:pass@example.com:123/one/two.three?q1=a1&q2=a2#body",
        "http://userid:password@example.com:8080",
        "http://userid:password@example.com:8080/",
        "http://userid@example.com",
        "http://userid@example.com/",

        "http://example#asd@asdasd",
        "http://usern%40me:password@example.com/",
        "http://username:8080?@example.com/", // '8080' is port, not password '8080?'
        "http://username:p@ssw@rd@example.com/", // League error
        'http://username:pass\word@example.com/', // work at linux, IExplorer. not in the Chrome. need percent-encode

        // in bash escape the '!', '[]', '$'
        "http://-.~_!'(&)*+$,;=:%40:80%2f::::::@example.com",
        "http://-.~_!'(&)*+$,;=:%40:80%2f:::%@example.com",

        "http://example.com:/asd",  // linux works, i. explorer no

                "http://142.42.1.1",
                "http://142.42.1.1/",
                "http://142.42.1.1/foo/bar/baz",
                "http://142.42.1.1:8080",
                "http://142.42.1.1:8080/",
                "http://142.42.1.1:8080/foo/bar/",
                "http://223.255.255.254",

                // https://en.wikipedia.org/wiki/IPv6_address
                "http://[1080:0:0:0:8:800:200C:417A]/index.html",
                "http://[1080::8:800:200C:417A]/foo",
                "http://[2010:836B:4179::836B:4179]",
                "http://[3ffe:2a00:100:7031::1]",
                "http://[::192.9.5.5]/ipng",
                "http://[::FFFF:129.144.52.38]:80/index.html",
                "http://[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:80/index.html",

                "http://[fc00::]",  // Unique local address
                "http://[fde4:8dba:82e1::]/index.html",
                "http://[fde4:8dba:82e1:ffff::]/das",
                "http://[fe80::1ff:fe23:4567:890a%25eth0]/",    // Link-local address
//                "http://[fe80--1ff-fe23-4567-890as3.ipv6-literal.net]/", // Uniform Naming Convention
                "http://[ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff]/",    // Multicast address

        "http://www.microsoft.xn--comindex-g03d.html.irongeek.com/", // YUCK!

        "http://xn--h32b13vza.xn--3e0b707e/",
        "http://xn--y3h.tk/",
//        "http://xn—y3h.tk/", ??
        "https://asd@xn----7sbbtkohtqhvkc8j.xn--p1ai",
        "http://nic.xn--unup4y",

        "http://nic.机构",
        "http://构.机构",
        "http://سورية.سورية",
        "http://ලංකා.ලංකා",
        "http://🤫🤫🤫.ws", // real works
        "http://☺.damowmow.com/", // real works
        "http://foo.com/unicode_(✪)_in_parens",
        "http://i♥usa.ws", // real works
        "http://тест.com",  // allow
        "http://тест.ws",   // disallow schema
        "http://test.ws/ёпрст",
        "http://правительство.рф/",
        "http://test.рф/",
        "http://test.фф/", // not real TLD
        "http://áéíóú.com",

        // equivalent next 2 below
        "http://bébé.be/toto/тестовый_путь/",
        "http://xn--bb-bjab.be/toto/%D1%82%D0%B5%D1%81%D1%82%D0%BE%D0%B2%D1%8B%D0%B9_%D0%BF%D1%83%D1%82%D1%8C/",

        // equivalent next 2 below
        "http://스타벅스코리아.com/to/the/sky/",
        "http://xn--oy2b35ckwhba574atvuzkc.com/path/../to/the/./sky/",

//        "http://www.example.xn--really-long-punycode-test-string-test-tests-123-tests/", // League normalize error

        "http://test@gmail.com",
        "http://a.b--c.de/",
        "https://website--884118290919845771573-gasstation.business.site", // real example
        "http://far_1371.rozblog.com/", // real example

        "ftp://cnn.example.com&story=breaking_news@10.0.0.1/top_story.htm", // 7.6.  Semantic Attacks
    ],



    'invalid' => [
        "http://жжжжо", // utf8 local domain
        "http://example．com", // Not NFC form
        "http://-example.com/",
        "http://-ex.example.com/",
        "http://example-.com/",
        "http://-example-.com/",
        "http://userid@/example.com", // no host when user or pass is empty
        "http://Http facebook",
//        "http://Http//facebook",
        "http://http:\\\\www.rizwanearning.com",
//        "http://cnn.example.com&story=breaking_news@10.0.0.1/top_story.htm",
//        "http://example.com/./",
//        "http://example.com/../",
//        "http://example.com/.../",
//        "http://example.com/...",
        "http://example..com/",
        "http://example...com/",
        "http://.example.com/", // but can auto-remove first dot in Chrome browser
        "http:://example.com//",
        "http$#://example.com/",
        "http//://example.com/",
        "http:/5/example.com/",
        "http%//example.com/",
        "http:%//example.com/",
        "http:////example.com",

        "http://example-bad-tld.1com",
        "http://example.55",
        "http://example.badport:80912",

        // valid for validator, but for project do not valid
//        "https://www.youtube.com/channel/UCJKRKUI7DqUahY0jhLXq4Uw https://www.facebook.com/groups/214287513601003",

        "http://username:password?@example.com/", // no valid host 'username'
        // https://support.microsoft.com/en-us/help/969869/certain-special-characters-are-not-allowed-in-the-url-entered-into-the
        "http://-.~_!'(&)*+$,;=:%40:80%2f:::?:::@example.com",
        "http://user:password:::#::@example.com", // allowed in curl, not at wget and browsers

//        "http://example.com/\"><script>alert(document.cook‌​ie)</script>\"",
        "http://www.example.xn--overly-long-punycode-test-string-test-tests-123-test-test12345/",
        "://example.com/",
        "//",
        "///",
        "//?a",
        ":// should fail",
        "file://example.com/image.png'",
        "ftps://foo.bar/",

        "h://test",
        "htt://google.com",
        "http:// shouldfail.com",
        "http://",
        "http://#",
        "http://##",
        "http://##/",
        "http://.",
        "http://..",
        "http://../",
        "http://.www.example.com/",  // TODO not valid?
        "http:///a",
        "http://?a",
        "http://??",
        "http://??/",
        "http://?example.?om/",
        "http://@",
//        "http://@a.ch/",
        "http://a@.ch/",
        "http://-a.b.co",
        "http://a.b-.co",
//        "http://a@@a.ch/",
//        "http://a@a@a.ch/",
        "http://abc..com/",
//        "http://foo.bar/foo(bar)baz quux",
//        "http://foo.bar?q=Spaces should be encoded",
        "https://-foo.com",
        "https://foo-.com",
//        "https://foo_bar",
//        "http://go/ogle.com",
//        "http://google.com/ /",
        "http://google\\.com",
        "http://google\".com",
        "http://google:.com",
        "http://google***.com",
        "http://<google>.com",
        "http://go|ogle.com",
        "http://www(google.com",
        "http://www=google.com",
        "https:/",
        'http://example\newline',
        "https://www.g.com/error\n/bleh/bleh",
        "rdar://1234",

        "http://➡/䨹", // local not latin, not ascii
//        "http://xny--y3h.tk/", // seems like non punycode variant
        "http://xn-y3h-.tk/",
        "http://-xn--y3h.tk/",

                "http://0.0.0.0",
                "http://1.1.1.1.1",
                "http://10.1.1.0",
//                "http://10.1.1.1",
//                "http://10.1.1.254",
                "http://10.1.1.255",

                "http://123.123.123",
                "http://142.42.1.1:8080:30/",
                "http://224.1.1.1",
                "http://362812.34",     // similar to bad tld
                "http://3628126748", // Technically valid though, workable curl/wget/Chrome, not IExplorer
                "http://900.900.900.900/",

//        "http://example.comhttp://local.com/resources/test_ozon_web.xml",
    ],

    'case_specific' => [
        // relative if disallow
        "/index.php",
        "/index.php?asd",
        "?index",
        "?index#tag1;12",
        "#sasd-sad!;.",
        "#asd?ququ:qoqo",
        "index",
        'C:\WORK\REPOS\SVN\ANAL-STAT\.gitignore',
        "http:example.com", // not HTTP URI
        "file:///etc/hosts",
    ]
];
