# 本ソースを以下の場所に設置(MacのApacheをいじる場合)

- /Library/WebServer/Documents/kubox(本プロジェクト)

# /etc/apache2/httpd.confを編集

```
LoadModule rewrite_module libexec/apache2/mod_rewrite.so
LoadModule php5_module libexec/apache2/libphp5.so
```

上記部分のコメントアウトを外す

`AllowOverride All`にする

`$ sudo apachectl restart`

# 本ソースを以下の場所に設置(MacのApacheをいじらない場合。設置場所は一例)

`~/git/kubox(本プロジェクト)`

# PHPで簡易Webサーバ起動(実機のみ可)

```
$ cd ~/git
$ php -S localhost:7777
```

# AndroidStudioで作成したエミュレータの場合(Url.javaを編集)

```Url.java   
} else if (BuildConfig.BUILD_TYPE == "stage") {
    return "http://10.0.2.2/kubox/practice/public/";
}
```

# 実機の場合(Url.javaを編集)

```Url.java
} else if (BuildConfig.BUILD_TYPE == "stage") {
    return "http://localhost:7777/kubox/practice/public/";
}
```

charlesでプロキシするか、
http://qiita.com/syarihu/items/23f5cd9edc9d081e0d65
にあるようにポートフォワーディングすればOK(inspectは閉じてはならない)

# 試しにアクセスし、Json結果が表示できればOK

エミュレータ: `http://10.0.2.2/kubox/practice/public/`   
実機: `http://localhost:7777/kubox/practice/public/items`
