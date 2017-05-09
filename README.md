# 本ソースを以下の場所に設置

- 自身のAndroidプロジェクトルート
- /Library/WebServer/Documents/kubox(本プロジェクト)

# /etc/apache2/httpd.confを編集

```
LoadModule rewrite_module libexec/apache2/mod_rewrite.so
LoadModule php5_module libexec/apache2/libphp5.so
```

のコメントアウトを外す
`AllowOverride All`にする

`$ sudo apachectl restart`

# AndroidStudioで作成したエミュレータの場合

`Url.java`
```
} else if (BuildConfig.BUILD_TYPE == "stage") {
    return "http://10.0.2.2/kubox/practice/public/";
}
```

# 実機の場合

`Url.java`
```
} else if (BuildConfig.BUILD_TYPE == "stage") {
    return "http://localhost8000/kubox/practice/public/";
}
```

charlesでプロキシするか、
http://qiita.com/syarihu/items/23f5cd9edc9d081e0d65
にあるようにポートフォワーディングすればOK(inspectは閉じてはならない)

# 試しにアクセスし、Json結果が表示できればOK

`http://localhost:8000/kubox/practice/public/items`
