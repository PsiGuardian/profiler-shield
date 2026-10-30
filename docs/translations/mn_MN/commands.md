# Тушаалуудын лавлах

[Баримт бичгийн агуулга](README.md)

Symfony-ийн үндсэн хавтсаас итгэмжлэгдсэн терминалд ажиллуулна. Жишээнүүд dev ашиглана. Нууц үг, түлхүүр, сэргээх кодыг аргумент, лог эсвэл Git-д бүү бич.

## Тушаал хайх ба тусламж


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev ба --no-interaction нь Symfony-ийн сонголтууд. --no-interaction нь нууц өгөхгүй, харилцан нууц үг асуух тушаалд бүү хэрэглэ. --help нь бичих хэлбэр, -v дэлгэрэнгүйг харуулна. Код 0 нь мэдээлсэн үр дүнг батална; --no-admin нь ашиглах порталгүй ч амжилттай байж болно.

## `vendor/bin/guardian`

Bundle-ийг dev/test-д идэвхжүүлж, кэш цэвэрлээд guardian:setup --automatic ажиллуулна. Дахин кэш цэвэрлэж guardian:doctor болон guardian:access ажиллуулна. Эхний алдаанд зогсоно. Давтахад бүртгэл, түлхүүр хадгалагдана. APP_ENV=prod-г татгалзана, proc_open шаардлагатай.

Аргументгүй, --help, -h эсвэл help нь зөвхөн тусламж харуулна. setup нь цорын ганц үйлдэл бөгөөд нэмэлт сонголт авахгүй.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Тушаалуудын лавлах |
|---|---|
| [`guardian:setup`](#guardiansetup) | Хувийн зам, түлхүүр, хадгалалт, Symfony файлууд болон байхгүй бол администратор бэлтгэнэ. |
| [`guardian:install`](#guardianinstall) | Хадгалалтыг бэлтгэж --username нэртэй администратор үүсгэнэ, анхдагч admin. |
| [`guardian:access`](#guardianaccess) | Самбарын зам болон анхны нэвтрэх мэдээллийн байршлыг харуулна, нууц үгийг харуулахгүй. |
| [`guardian:doctor`](#guardiandoctor) | Хадгалалт, эрх, түлхүүр, зам, идэвхтэй администратор болон тохиргоог шалгана. |
| [`guardian:password:reset`](#guardianpasswordreset) | Байгаа username бүртгэлийн нууц үгийг бодлогын дагуу далд оруулж өөрчилнө, бүх сессийг хүчингүй болгож үйлдлийг бүртгэнэ. |
| [`guardian:mfa:reset`](#guardianmfareset) | username-ийн TOTP нууц ба тоолуурыг арилгаж, сессүүдийг хүчингүй болгон бүртгэнэ. |
| [`guardian:login:unlock`](#guardianloginunlock) | username-ийн тоолууруудыг тэглэнэ. |
| [`guardian:policy:reset`](#guardianpolicyreset) | IP жагсаалтыг хоосолж, хуваарь, JIT болон алсын яаралтай хандалтыг унтраана. |
| [`guardian:lockdown`](#guardianlockdown) | on нь оношилгоог хааж бүх сессийг устгана; бизнесийн хуудас ажиллана. |
| [`guardian:emergency`](#guardianemergency) | Байгаа username-д 5 нэг удаагийн код үүсгэж, хуучныг солино. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Хүлээгдэж буй дохиог PHP mail()-аар alert_email рүү илгээнэ. |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Хэрэглэгчийн нууц үг бус хиймэл нууцаар хэш баталгаажуулалтыг хэмжинэ. |
| [`guardian:uninstall`](#guardianuninstall) | Guardian үүсгэсэн нь нотлогдох файлыг устгахад бэлтгэнэ. |

## `guardian:setup`

Хувийн зам, түлхүүр, хадгалалт, Symfony файлууд болон байхгүй бол администратор бэлтгэнэ. Бүртгэлүүдийг хадгална. Үйлдвэрлэлийн орчинд татгалзана. Шууд ажиллуулсны дараа кэш цэвэрлэж шалгалтуудыг ажиллуулна.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic нь admin болон нууцыг хувийн 0600 файлд үүсгэнэ. --path нь 25–181 тэмдэгтийн зам сонгож, байгаа замыг солихгүй. --no-admin нь администратор үүсгэхгүй; идэвхтэй бүртгэлгүй бол портал ашиглах боломжгүй. --username нь харилцан оруулах нэрийн санал, анхдагч admin; автомат бүртгэлийн нэрийг өөрчлөхгүй.

```text
--path: ^/[a-zA-Z0-9/_-]{24,180}$
/_profiler; /_wdt → ∅
```

```bash
php bin/console guardian:setup --username=operator --env=dev
php bin/console cache:clear --env=dev
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## `guardian:install`

Хадгалалтыг бэлтгэж --username нэртэй администратор үүсгэнэ, анхдагч admin. Идэвхтэй бодлогын дагуу далд нууц үг асууна. Байгаа бүртгэлийг татгалзана. Зам болон Symfony тохиргоо үүсгэхгүй; ердийн үед бүрэн туслахыг ашиглана.


```bash
php bin/console guardian:install --username=admin --env=dev
```


хэрэглэгчийн нэр : 3 Үнэгүй 64 Алфаномер хэлбэр, . _ -

## `guardian:access`

Самбарын зам болон анхны нэвтрэх мэдээллийн байршлыг харуулна, нууц үгийг харуулахгүй. --base-url нь бодит HTTPS эх хаяг, сонголтоор порт авна; нэвтрэх мэдээлэл, асуулга, тасархай хүлээн авахгүй. Жишээ хостыг солино.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Хадгалалт, эрх, түлхүүр, зам, идэвхтэй администратор болон тохиргоог шалгана. Асуудал байвал гарах код 1. PHP CLI ба PHP-FPM өөр цагийн бүстэй байж болно. Интернэтэд ил байдал эсвэл прокси дүрмийг шалгахгүй.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Байгаа username бүртгэлийн нууц үгийг бодлогын дагуу далд оруулж өөрчилнө, бүх сессийг хүчингүй болгож үйлдлийг бүртгэнэ. Нууц үгийг аргументаар бүү дамжуул.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

username-ийн TOTP нууц ба тоолуурыг арилгаж, сессүүдийг хүчингүй болгон бүртгэнэ. Шинэ баталгаажуулагч бүртгэнэ; нийт MFA шаардлага хэвээр тул профайлерыг хааж болно.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

username-ийн тоолууруудыг тэглэнэ. --ip ХАЯГ нь тухайн IPv4/IPv6 эх үүсвэрийн тоолуурыг бас арилгана; үгүй бол сүлжээний түгжээ үлдэж болно. --all бүх тоолуурыг арилгана. Бүртгэл, нууц үг, сесс өөрчлөгдөхгүй; үйлдэл бүртгэгдэнэ.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

IP жагсаалтыг хоосолж, хуваарь, JIT болон алсын яаралтай хандалтыг унтраана. Нэвтрэлт, MFA, нийт түгжээ болон нэвтрэх түгжээ үлдэнэ. Сэргээсний дараа шаардлагатай хязгаарлалтыг самбарт дахин тохируулна.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on нь оношилгоог хааж бүх сессийг устгана; бизнесийн хуудас ажиллана. off нь зөвхөн нийт түгжээг тайлна: дахин нэвтрэх шаардлагатай, бусад бодлого хүчинтэй.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Байгаа username-д 5 нэг удаагийн код үүсгэж, хуучныг солино. Нэг удаа харуулна; серверээс гадуур нууцын менежерт хадгална. Алсын хэрэглээнд яаралтай хандалт идэвхтэй, баталгаажсан сесс, нууц үг, шалтгаан шаардлагатай. Нийт түгжээг тойрохгүй.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Хүлээгдэж буй дохиог PHP mail()-аар alert_email рүү илгээнэ. Дамжуулалт болон хуваарьт даалгаврыг тохируулж, бодит хүргэлтийг шалгана. Амжилтгүй дохио дараалалд үлдэнэ. Хүлээн авагч буруу эсвэл илгээх алдаанд код 1.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Хэрэглэгчийн нууц үг бус хиймэл нууцаар хэш баталгаажуулалтыг хэмжинэ. --iterations: 1–10, анхдагч 3. Алгоритм, хугацаа, секунд дэх шалгалттай JSON гаргана. Нэг дотоод CPU процессыг хэмжинэ, GPU эсвэл халдагчийг биш.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Guardian үүсгэсэн нь нотлогдох файлыг устгахад бэлтгэнэ. Бүртгэл, тохиргоо, лог, түлхүүрийг хадгална. Ажлын процессуудыг зогсоож composer remove-оос өмнө ажиллуулаад dev кэш цэвэрлэж дахин эхлүүлнэ. Өөрчилсөн болон гарал нь тодорхойгүй файлуудыг шалгана.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge нь анхдагчаар татгалзах баталгаатай өгөгдөл устгал хүснэ. --force нь --purge шаарддаг, устгалыг батална; --no-interaction-тай заавал хэрэгтэй. Гадаад хадгалалт, холбоос, үл мэдэх файл бүрэн устгалд саад болж болно. Хэрэгтэй түлхүүрийг хадгалж үр дүнг шалгана.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Устгах ба өгөгдөл


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Хадгалалт ба түлхүүрийг хамт нөөцөлж сэргээхийг туршина. Алдсан түлхүүр автоматаар шинээр үүсэхгүй. Устгасны дараа профайлерыг зөвхөн хаалттай орчинд шалгана. Үйлдвэрлэл: APP_DEBUG=0, WebProfilerBundle ба цуглуулалт унтраалттай. Дотоод аудитын гинж халдлагад өртсөн хостын бүрэн бүтэн байдлыг нотлохгүй.

[Аж үйлдвэрийн болон мэдээлэл](desinstallation.md)
