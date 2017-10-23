# telescr.in
Blockchain based application for creating text pages. First application on orwell blockchain. [Whats is Telescreen](https://en.wikipedia.org/wiki/Telescreen)

You can watch application at url [Telescr.in](https://telescr.in). Based on [datascript technology](https://github.com/gettocat/orwelldb) and [orwell blockchain](https://github.com/gettocat/orwell).



## Usage guide


### Create
1. Go to page [Create new page](https://telescr.in)
2. Fill form and click *Add page*
3. If you dont have errors with data - you will be redirected to new created page with you input data.
3.1 Creation does not mean that your entry is in the blockchain. Creation only tells orwell about existence of your data and relay a transaction. After 5-10 minutes, your transaction will be included in the block. Information of inclusion can be found on the website [orwellscan.org](http://orwellscan.org)


### Editing
1. For edit page you need key, that system write on your cookie. If you creator - just visit page, what you want to edit and click button *edit*. In other case - ask creator for pageId.
1.1 pageId can be finded in cookie: page_{oid_of_page}.
1.2 Visit url: http://telescr.in/{page_oid}?key={pageId} and you will get the privilege to edit this page.
2. Just edit title and content of page (and you can change password, if page have password).


### Encryption
Page content encrypted with AES-CBC cypher. Encrypted fileds: title, author and content. if the page is encrypted - every time you login to the page, you must enter the password which was used for encryption.

Encryption and decryption occurs in the browser of the creator of the page, passwords are not transmitted over the network.

### Edit encrypted page
If you have a password from an encrypted page - it does not mean that you can edit it. You also need a pageId from this page. \
While editing the page, you can change the password. By default, the old password remains on the encrypted page and encryption is repeated using it. You can not remove the password or set a password on an unencrypted page.
