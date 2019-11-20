<?php
/**
 * Created by PhpStorm.
 * User: linkuha (Pudich Aleksandr)
 * Date: 19.04.2019
 * Time: 14:27
 */

return [
    'http://example/begin/cho' =>
     [
         // relative
         "/index.php",
         "/index.php?asd",
         "/b/./s/../..ss/index.php?need=TITS",
         "b/./s/../..ss/index.php?need=TITS",
         "./b/./s/../..ss/index.php?need=TITS",
         "/../b/./s/../..ss/index.php?need=TITS",
         "../b/./s/../..ss/index.php?need=TITS",
         "?index",
         "?index#tag1;12",
         "#sasd-sad!;.",
         "#asd?ququ:qoqo",
         "index",
     ],
    'http://example/begin/second/' =>
     [
         // relative
         "/index.php",
         "/index.php?asd",
         "/b/./s/../..ss/index.php?need=TITS",
         "b/./s/../..ss/index.php?need=TITS",
         "./b/./s/../..ss/index.php?need=TITS",
         "/../b/./s/../..ss/index.php?need=TITS",
         "../b/./s/../..ss/index.php?need=TITS",
         "?index",
         "/?index",
         "?index#tag1;12",
         "#sasd-sad!;.",
         "/#sasd-sad!;.",
         "#asd?ququ:qoqo",
         "index",
     ],

    'http://example/begin/cho.php' =>
     [
         // relative
         "/index.php",
         "/index.php?asd",
         "/b/./s/../..ss/index.php?need=TITS",
         "b/./s/../..ss/index.php?need=TITS",
         "./b/./s/../..ss/index.php?need=TITS",
         "/../b/./s/../..ss/index.php?need=TITS",
         "../b/./s/../..ss/index.php?need=TITS",
         "?index",
         "/?index",
         "?index#tag1;12",
         "#sasd-sad!;.",
         "/#sasd-sad!;.",
         "#asd?ququ:qoqo",
         "index",
     ],
    '//example.com/test/?query1=asd' =>
    [
// relative
            "/index.php",
            "/index.php?query2=dsa",
            "/b/./s/../..ss/index.php?need=TITS",
            "b/./s/../..ss/index.php?need=TITS",
            "./b/./s/../..ss/index.php?need=TITS",
            "/../b/./s/../..ss/index.php?need=TITS",
            "../b/./s/../..ss/index.php?need=TITS",
            "?index123",
            "/?index",
            "?index#tag1;12",
            "#sasd-sad!;.",
            "/#sasd-sad!;.",
            "#asd?ququ:qoqo",
            "index",
    ]
];
