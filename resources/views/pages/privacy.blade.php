<?php declare(strict_types=1);

use Core\Resources\RouteNames;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

?>

@extends(ViewNames::LAYOUTS_APP)

@push(SectionNames::META)
    <link rel="canonical"
          href="{{ route(RouteNames::PRIVACY) }}" />
@endpush

@section(SectionNames::CONTENT)
    @if(app::roleDecorator()->canEditTemplates())
        <page-editor :template="'{{ ViewNames::PAGES_PRIVACY }}'"></page-editor>
    @endif
    <h1 class="page-title">Политика в&nbsp;отношении обработки персональных данных</h1>
    <div id="inputResult">
        <div class="row mb-4">
            <div class="col">
                <h5>1. Общие положения</h5>
                <div class="descr">
                    Настоящая политика обработки персональных данных составлена
                    в&nbsp;соответствии с&nbsp;требованиями Федерального закона от&nbsp;27.07.2006.
                    №&nbsp;152-ФЗ «О&nbsp;персональных данных» (далее&nbsp;—
                    Закон о&nbsp;персональных данных) и&nbsp;определяет порядок обработки
                    персональных данных и&nbsp;меры по&nbsp;обеспечению безопасности персональных
                    данных, предпринимаемые <span class="link mark owner-name-field"
                                                  id="owner-name-value"
                                                  data-scroll-to="#owner-name-field">Порочкиным Максимом Игоревичем</span> (далее&nbsp;— Оператор).
                </div>
                <div class="ol">
                    <div class="li">
                        1.1. Оператор ставит своей важнейшей целью и&nbsp;условием осуществления
                        своей деятельности соблюдение прав и&nbsp;свобод человека и&nbsp;гражданина
                        при обработке его персональных данных, в&nbsp;том числе защиты прав
                        на&nbsp;неприкосновенность частной жизни, личную и&nbsp;семейную тайну.
                    </div>
                    <div class="li">
                        1.2. Настоящая политика Оператора в&nbsp;отношении обработки персональных
                        данных (далее&nbsp;— Политика) применяется ко&nbsp;всей информации,
                        которую Оператор может получить о&nbsp;посетителях веб-сайта
                        <span class="link mark owner-site-url-field"
                              data-scroll-to="#owner-site-url-field">https://sputnik-iskozh.ru/</span>.
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <h5>2. Основные понятия, используемые в&nbsp;Политике</h5>
                <div class="ol">
                    <div class="li">
                        2.1. Автоматизированная обработка персональных
                        данных&nbsp;— обработка персональных данных с&nbsp;помощью средств
                        вычислительной техники.
                    </div>
                    <div class="li">
                        2.2. Блокирование персональных данных&nbsp;— временное прекращение
                        обработки персональных данных (за&nbsp;исключением случаев, если обработка
                        необходима для уточнения персональных данных).
                    </div>
                    <div class="li">
                        2.3. Веб-сайт&nbsp;— совокупность графических и&nbsp;информационных
                        материалов, а&nbsp;также программ для ЭВМ и&nbsp;баз данных, обеспечивающих
                        их&nbsp;доступность в&nbsp;сети интернет по&nbsp;сетевому адресу
                        <span class="link mark owner-site-url-field"
                              data-scroll-to="#owner-site-url-field">https://sputnik-iskozh.ru/</span>.
                    </div>
                    <div class="li">
                        2.4. Информационная система персональных данных&nbsp;— совокупность
                        содержащихся в&nbsp;базах данных персональных данных и&nbsp;обеспечивающих
                        их&nbsp;обработку информационных технологий и&nbsp;технических средств.
                    </div>
                    <div class="li">
                        2.5. Обезличивание персональных данных&nbsp;— действия,
                        в&nbsp;результате которых невозможно определить без использования
                        дополнительной информации принадлежность персональных данных конкретному
                        Пользователю или иному субъекту персональных данных.
                    </div>
                    <div class="li">
                        2.6. Обработка персональных данных&nbsp;— любое действие (операция)
                        или совокупность действий (операций), совершаемых с&nbsp;использованием
                        средств автоматизации или без использования таких средств
                        с&nbsp;персональными данными, включая сбор, запись, систематизацию,
                        накопление, хранение, уточнение (обновление, изменение), извлечение,
                        использование, передачу (распространение, предоставление, доступ),
                        обезличивание, блокирование, удаление, уничтожение персональных данных.
                    </div>
                    <div class="li">
                        2.7. Оператор&nbsp;— государственный орган, муниципальный орган,
                        юридическое или физическое лицо, самостоятельно или совместно с&nbsp;другими
                        лицами организующие и/или&nbsp;осуществляющие обработку персональных данных,
                        а&nbsp;также определяющие цели обработки персональных данных, состав
                        персональных данных, подлежащих обработке, действия (операции), совершаемые
                        с&nbsp;персональными данными.
                    </div>
                    <div class="li">2.8. Персональные данные&nbsp;— любая информация,
                                    относящаяся прямо или косвенно к&nbsp;определенному или определяемому
                                    Пользователю веб-сайта <span class="link mark owner-site-url-field"
                                                                 data-scroll-to="#owner-site-url-field">https://sputnik-iskozh.ru/</span>.
                    </div>
                    <div class="li">
                        2.9. Персональные данные, разрешенные субъектом персональных данных для
                        распространения,&nbsp;— персональные данные, доступ неограниченного
                        круга лиц к&nbsp;которым предоставлен субъектом персональных данных путем
                        дачи согласия на&nbsp;обработку персональных данных, разрешенных субъектом
                        персональных данных для распространения в&nbsp;порядке, предусмотренном
                        Законом о&nbsp;персональных данных (далее&nbsp;— персональные данные,
                        разрешенные для распространения).
                    </div>
                    <div class="li">
                        2.10. Пользователь&nbsp;— любой посетитель веб-сайта
                        <span class="link mark owner-site-url-field"
                              data-scroll-to="#owner-site-url-field">https://sputnik-iskozh.ru/</span>.
                    </div>
                    <div class="li">
                        2.11. Предоставление персональных данных&nbsp;— действия, направленные
                        на&nbsp;раскрытие персональных данных определенному лицу или определенному
                        кругу лиц.
                    </div>
                    <div class="li">
                        2.12. Распространение персональных данных&nbsp;— любые действия,
                        направленные на&nbsp;раскрытие персональных данных неопределенному кругу лиц
                        (передача персональных данных) или на&nbsp;ознакомление с&nbsp;персональными
                        данными неограниченного круга лиц, в&nbsp;том числе обнародование
                        персональных данных в&nbsp;средствах массовой информации, размещение
                        в&nbsp;информационно-телекоммуникационных сетях или предоставление доступа
                        к&nbsp;персональным данным каким-либо иным способом.
                    </div>
                    <div class="li">
                        2.13. Трансграничная передача персональных данных&nbsp;— передача
                        персональных данных на&nbsp;территорию иностранного государства органу
                        власти иностранного государства, иностранному физическому или иностранному
                        юридическому лицу.
                    </div>
                    <div class="li">
                        2.14. Уничтожение персональных данных&nbsp;— любые действия,
                        в&nbsp;результате которых персональные данные уничтожаются безвозвратно
                        с&nbsp;невозможностью дальнейшего восстановления содержания персональных
                        данных в&nbsp;информационной системе персональных данных
                        и/или&nbsp;уничтожаются
                        материальные носители персональных данных.
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <h5>3. Основные права и&nbsp;обязанности Оператора</h5>
                <div class="ol">
                    <div class="li">3.1. Оператор имеет право:</div>
                    <div class="li">
                        —&nbsp;получать от&nbsp;субъекта персональных данных достоверные
                        информацию и/или&nbsp;документы, содержащие персональные данные;
                    </div>
                    <div class="li">
                        —&nbsp;в&nbsp;случае отзыва субъектом персональных данных согласия
                        на&nbsp;обработку персональных данных, а&nbsp;также, направления обращения
                        с&nbsp;требованием о&nbsp;прекращении обработки персональных данных,
                        Оператор вправе продолжить обработку персональных данных без согласия
                        субъекта персональных данных при наличии оснований, указанных в&nbsp;Законе
                        о&nbsp;персональных данных;
                    </div>
                    <div class="li">
                        —&nbsp;самостоятельно определять состав и&nbsp;перечень мер,
                        необходимых и&nbsp;достаточных для обеспечения выполнения обязанностей,
                        предусмотренных Законом о&nbsp;персональных данных и&nbsp;принятыми
                        в&nbsp;соответствии с&nbsp;ним нормативными правовыми актами, если иное
                        не&nbsp;предусмотрено Законом о&nbsp;персональных данных или другими
                        федеральными законами.
                    </div>
                    <div class="li">3.2. Оператор обязан:</div>
                    <div class="li">
                        —&nbsp;предоставлять субъекту персональных данных по&nbsp;его просьбе
                        информацию, касающуюся обработки его персональных данных;
                    </div>
                    <div class="li">
                        —&nbsp;организовывать обработку персональных данных в&nbsp;порядке,
                        установленном действующим законодательством&nbsp;РФ;
                    </div>
                    <div class="li">
                        —&nbsp;отвечать на&nbsp;обращения и&nbsp;запросы субъектов
                        персональных данных и&nbsp;их&nbsp;законных представителей
                        в&nbsp;соответствии с&nbsp;требованиями Закона о&nbsp;персональных данных;
                    </div>
                    <div class="li">
                        —&nbsp;сообщать в&nbsp;уполномоченный орган по&nbsp;защите прав
                        субъектов персональных данных по&nbsp;запросу этого органа необходимую
                        информацию в&nbsp;течение 10&nbsp;дней с&nbsp;даты получения такого запроса;
                    </div>
                    <div class="li">
                        —&nbsp;публиковать или иным образом обеспечивать неограниченный доступ
                        к&nbsp;настоящей Политике в&nbsp;отношении обработки персональных данных;
                    </div>
                    <div class="li">
                        —&nbsp;принимать правовые, организационные и&nbsp;технические меры для
                        защиты персональных данных от&nbsp;неправомерного или случайного доступа
                        к&nbsp;ним, уничтожения, изменения, блокирования, копирования,
                        предоставления, распространения персональных данных, а&nbsp;также
                        от&nbsp;иных неправомерных действий в&nbsp;отношении персональных данных;
                    </div>
                    <div class="li">
                        —&nbsp;прекратить передачу (распространение, предоставление, доступ)
                        персональных данных, прекратить обработку и&nbsp;уничтожить персональные
                        данные в&nbsp;порядке и&nbsp;случаях, предусмотренных Законом
                        о&nbsp;персональных данных;
                    </div>
                    <div class="li">
                        —&nbsp;исполнять иные обязанности, предусмотренные Законом
                        о&nbsp;персональных данных.
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <h5>4. Основные права и&nbsp;обязанности субъектов персональных данных</h5>
                <div class="ol">
                    <div class="li">4.1. Субъекты персональных данных имеют право:</div>
                    <div class="li">
                        —&nbsp;получать информацию, касающуюся обработки его персональных
                        данных, за&nbsp;исключением случаев, предусмотренных федеральными законами.
                        Сведения предоставляются субъекту персональных данных Оператором
                        в&nbsp;доступной форме, и&nbsp;в&nbsp;них не&nbsp;должны содержаться
                        персональные данные, относящиеся к&nbsp;другим субъектам персональных
                        данных, за&nbsp;исключением случаев, когда имеются законные основания для
                        раскрытия таких персональных данных. Перечень информации и&nbsp;порядок
                        ее&nbsp;получения установлен Законом о&nbsp;персональных данных;
                    </div>
                    <div class="li">
                        —&nbsp;требовать от&nbsp;оператора уточнения его персональных данных,
                        их&nbsp;блокирования или уничтожения в&nbsp;случае, если персональные данные
                        являются неполными, устаревшими, неточными, незаконно полученными или
                        не&nbsp;являются необходимыми для заявленной цели обработки, а&nbsp;также
                        принимать предусмотренные законом меры по&nbsp;защите своих прав;
                    </div>
                    <div class="li">
                        —&nbsp;выдвигать условие предварительного согласия при обработке
                        персональных данных в&nbsp;целях продвижения на&nbsp;рынке товаров, работ
                        и&nbsp;услуг;
                    </div>
                    <div class="li">
                        —&nbsp;на&nbsp;отзыв согласия на&nbsp;обработку персональных данных,
                        а&nbsp;также, на&nbsp;направление требования о&nbsp;прекращении обработки
                        персональных данных;
                    </div>
                    <div class="li">
                        —&nbsp;обжаловать в&nbsp;уполномоченный орган по&nbsp;защите прав
                        субъектов персональных данных или в&nbsp;судебном порядке неправомерные
                        действия или бездействие Оператора при обработке его персональных данных;
                    </div>
                    <div class="li">
                        —&nbsp;на&nbsp;осуществление иных прав, предусмотренных
                        законодательством&nbsp;РФ.
                    </div>
                    <div class="li">4.2. Субъекты персональных данных обязаны:</div>
                    <div class="li">
                        —&nbsp;предоставлять Оператору достоверные данные о&nbsp;себе;
                    </div>
                    <div class="li">
                        —&nbsp;сообщать Оператору об&nbsp;уточнении (обновлении, изменении)
                        своих персональных данных.
                    </div>
                    <div class="li">
                        4.3. Лица, передавшие Оператору недостоверные сведения о&nbsp;себе, либо
                        сведения о&nbsp;другом субъекте персональных данных без согласия последнего,
                        несут ответственность в&nbsp;соответствии с&nbsp;законодательством&nbsp;РФ.
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <h5>5. Принципы обработки персональных данных</h5>
                <div class="ol">
                    <div class="li">
                        5.1. Обработка персональных данных осуществляется на&nbsp;законной
                        и&nbsp;справедливой основе.
                    </div>
                    <div class="li">
                        5.2. Обработка персональных данных ограничивается достижением конкретных,
                        заранее определенных и&nbsp;законных целей. Не&nbsp;допускается обработка
                        персональных данных, несовместимая с&nbsp;целями сбора персональных данных.
                    </div>
                    <div class="li">
                        5.3. Не&nbsp;допускается объединение баз данных, содержащих персональные
                        данные, обработка которых осуществляется в&nbsp;целях, несовместимых между
                        собой.
                    </div>
                    <div class="li">
                        5.4. Обработке подлежат только персональные данные, которые отвечают целям
                        их&nbsp;обработки.
                    </div>
                    <div class="li">
                        5.5. Содержание и&nbsp;объем обрабатываемых персональных данных
                        соответствуют заявленным целям обработки. Не&nbsp;допускается избыточность
                        обрабатываемых персональных данных по&nbsp;отношению к&nbsp;заявленным целям
                        их&nbsp;обработки.
                    </div>
                    <div class="li">
                        5.6. При обработке персональных данных обеспечивается точность персональных
                        данных, их&nbsp;достаточность, а&nbsp;в&nbsp;необходимых случаях
                        и&nbsp;актуальность по&nbsp;отношению к&nbsp;целям обработки персональных
                        данных. Оператор принимает необходимые меры и/или&nbsp;обеспечивает
                        их&nbsp;принятие по&nbsp;удалению или уточнению неполных или неточных
                        данных.
                    </div>
                    <div class="li">
                        5.7. Хранение персональных данных осуществляется в&nbsp;форме, позволяющей
                        определить субъекта персональных данных, не&nbsp;дольше, чем этого требуют
                        цели обработки персональных данных, если срок хранения персональных данных
                        не&nbsp;установлен федеральным законом, договором, стороной которого,
                        выгодоприобретателем или поручителем по&nbsp;которому является субъект
                        персональных данных. Обрабатываемые персональные данные уничтожаются либо
                        обезличиваются по&nbsp;достижении целей обработки или в&nbsp;случае утраты
                        необходимости в&nbsp;достижении этих целей, если иное не&nbsp;предусмотрено
                        федеральным законом.
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <h5>6. Цели обработки персональных данных</h5>
                <div class="ol">
                    <div class="li"
                         id="8RpUKbH">
                        <table class="table">
                            <tbody>
                            <tr>
                                <th>Цель обработки</th>
                                <td class="purpose-field">
                                    <ul>
                                        <li>
                                            <span class="mark link">предоставление доступа Пользователю к&nbsp;сервисам, информации и/или&nbsp;материалам, содержащимся на&nbsp;веб-сайте</span>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <th>Персональные данные</th>
                                <td class="data-field">
                                    <ul>
                                        <li><span class="mark link">фамилия, имя, отчество</span></li>
                                        <li><span class="mark link">электронный адрес</span></li>
                                        <li><span class="mark link">номера телефонов</span></li>
                                        <li><span class="mark link">год, месяц, дата и&nbsp;место рождения</span></li>
                                        <li><span class="mark link">фотографии</span></li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <th>Правовые основания</th>
                                <td class="legal-field">
                                    <ul>
                                        <li>
                                            <span class="mark link">договоры, заключаемые между оператором и&nbsp;субъектом персональных данных</span>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <th>Виды обработки персональных данных</th>
                                <td class="types-field">
                                    <ul>
                                        <li>
                                            <span class="mark link">Сбор, запись, систематизация, накопление, хранение, уничтожение и&nbsp;обезличивание персональных данных</span>
                                        </li>
                                        <li>
                                            <span class="mark link">Отправка информационных писем на&nbsp;адрес электронной почты</span>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="purpose-table-template"
                         style="display: none;">
                        <div class="li">
                            <table class="purpose-table">
                                <tbody>
                                <tr>
                                    <th>Цель обработки</th>
                                    <td class="purpose-field"></td>
                                </tr>
                                <tr>
                                    <th>Персональные данные</th>
                                    <td class="data-field"></td>
                                </tr>
                                <tr>
                                    <th>Правовые основания</th>
                                    <td class="legal-field"></td>
                                </tr>
                                <tr>
                                    <th>Виды обработки персональных данных</th>
                                    <td class="types-field"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <h5>7. Условия обработки персональных данных</h5>
                <div class="ol">
                    <div class="li">
                        7.1. Обработка персональных данных осуществляется с&nbsp;согласия субъекта
                        персональных данных на&nbsp;обработку его персональных данных.
                    </div>
                    <div class="li">
                        7.2. Обработка персональных данных необходима для достижения целей,
                        предусмотренных международным договором Российской Федерации или законом,
                        для осуществления возложенных законодательством Российской Федерации
                        на&nbsp;оператора функций, полномочий и&nbsp;обязанностей.
                    </div>
                    <div class="li">
                        7.3. Обработка персональных данных необходима для осуществления правосудия,
                        исполнения судебного акта, акта другого органа или должностного лица,
                        подлежащих исполнению в&nbsp;соответствии с&nbsp;законодательством
                        Российской Федерации об&nbsp;исполнительном производстве.
                    </div>
                    <div class="li">
                        7.4. Обработка персональных данных необходима для исполнения договора,
                        стороной которого либо выгодоприобретателем или поручителем по&nbsp;которому
                        является субъект персональных данных, а&nbsp;также для заключения договора
                        по&nbsp;инициативе субъекта персональных данных или договора,
                        по&nbsp;которому субъект персональных данных будет являться
                        выгодоприобретателем или поручителем.
                    </div>
                    <div class="li">
                        7.5. Обработка персональных данных необходима для осуществления прав
                        и&nbsp;законных интересов оператора или третьих лиц либо для достижения
                        общественно значимых целей при условии, что при этом не&nbsp;нарушаются
                        права и&nbsp;свободы субъекта персональных данных.
                    </div>
                    <div class="li">
                        7.6. Осуществляется обработка персональных данных, доступ неограниченного
                        круга лиц к&nbsp;которым предоставлен субъектом персональных данных либо
                        по&nbsp;его просьбе (далее&nbsp;— общедоступные персональные данные).
                    </div>
                    <div class="li">
                        7.7. Осуществляется обработка персональных данных, подлежащих опубликованию
                        или обязательному раскрытию в&nbsp;соответствии с&nbsp;федеральным законом.
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <h5>
                    8. Порядок сбора, хранения, передачи и&nbsp;других видов обработки персональных
                    данных
                </h5>
                <div class="descr">
                    Безопасность персональных данных, которые обрабатываются Оператором,
                    обеспечивается путем реализации правовых, организационных и&nbsp;технических
                    мер, необходимых для выполнения в&nbsp;полном объеме требований действующего
                    законодательства в&nbsp;области защиты персональных данных.
                </div>
                <div class="ol">
                    <div class="li">
                        8.1. Оператор обеспечивает сохранность персональных данных и&nbsp;принимает
                        все возможные меры, исключающие доступ к&nbsp;персональным данным
                        неуполномоченных лиц.
                    </div>
                    <div class="li">
                        8.2. Персональные данные Пользователя никогда, ни&nbsp;при каких условиях
                        не&nbsp;будут переданы третьим лицам, за&nbsp;исключением случаев, связанных
                        с&nbsp;исполнением действующего законодательства либо в&nbsp;случае, если
                        субъектом персональных данных дано согласие Оператору на&nbsp;передачу
                        данных третьему лицу для исполнения обязательств
                        по&nbsp;гражданско-правовому договору.
                    </div>
                    <div class="li">
                        8.3. В&nbsp;случае выявления неточностей в&nbsp;персональных данных,
                        Пользователь может актуализировать их&nbsp;самостоятельно, путем направления
                        Оператору уведомление на&nbsp;адрес электронной почты Оператора
                        <span class="link mark owner-email-field"
                              data-scroll-to="#owner-email-field">support@sputnik-iskozh.ru</span>
                        с&nbsp;пометкой «Актуализация персональных данных».
                    </div>
                    <div class="li">
                        8.4. Срок обработки персональных данных определяется достижением целей, для
                        которых были собраны персональные данные, если иной срок
                        не&nbsp;предусмотрен договором или действующим
                        законодательством.
                        <br>
                        Пользователь может в&nbsp;любой момент отозвать свое согласие
                        на&nbsp;обработку персональных данных, направив Оператору уведомление
                        посредством электронной почты на&nbsp;электронный адрес Оператора
                        <span class="link mark owner-email-field"
                              data-scroll-to="#owner-email-field">support@sputnik-iskozh.ru</span>
                        с&nbsp;пометкой «Отзыв согласия на&nbsp;обработку персональных
                        данных».
                    </div>
                    <div class="li">
                        8.5. Вся информация, которая собирается сторонними сервисами, в&nbsp;том
                        числе платежными системами, средствами связи и&nbsp;другими поставщиками
                        услуг, хранится и&nbsp;обрабатывается указанными лицами (Операторами)
                        в&nbsp;соответствии с&nbsp;их&nbsp;Пользовательским соглашением
                        и&nbsp;Политикой конфиденциальности. Субъект персональных данных
                        и/или&nbsp;с&nbsp;указанными документами. Оператор не&nbsp;несет
                        ответственность за&nbsp;действия третьих лиц, в&nbsp;том числе указанных
                        в&nbsp;настоящем пункте поставщиков услуг.
                    </div>
                    <div class="li">
                        8.6. Установленные субъектом персональных данных запреты на&nbsp;передачу
                        (кроме предоставления доступа), а&nbsp;также на&nbsp;обработку или условия
                        обработки (кроме получения доступа) персональных данных, разрешенных для
                        распространения, не&nbsp;действуют в&nbsp;случаях обработки персональных
                        данных в&nbsp;государственных, общественных и&nbsp;иных публичных интересах,
                        определенных законодательством&nbsp;РФ.
                    </div>
                    <div class="li">
                        8.7. Оператор при обработке персональных данных обеспечивает
                        конфиденциальность персональных данных.
                    </div>
                    <div class="li">
                        8.8. Оператор осуществляет хранение персональных данных в&nbsp;форме,
                        позволяющей определить субъекта персональных данных, не&nbsp;дольше, чем
                        этого требуют цели обработки персональных данных, если срок хранения
                        персональных данных не&nbsp;установлен федеральным законом, договором,
                        стороной которого, выгодоприобретателем или поручителем по&nbsp;которому
                        является субъект персональных данных.
                    </div>
                    <div class="li">
                        8.9. Условием прекращения обработки персональных данных может являться
                        достижение целей обработки персональных данных, истечение срока действия
                        согласия субъекта персональных данных, отзыв согласия субъектом персональных
                        данных или требование о&nbsp;прекращении обработки персональных данных,
                        а&nbsp;также выявление неправомерной обработки персональных данных.
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <h5>
                    9. Перечень действий, производимых Оператором с&nbsp;полученными персональными
                    данными
                </h5>
                <div class="ol">
                    <div class="li">
                        9.1. Оператор осуществляет сбор, запись, систематизацию, накопление,
                        хранение, уточнение (обновление, изменение), извлечение, использование,
                        передачу (распространение, предоставление, доступ), обезличивание,
                        блокирование, удаление и&nbsp;уничтожение персональных данных.
                    </div>
                    <div class="li">
                        9.2. Оператор осуществляет автоматизированную обработку персональных данных
                        с&nbsp;получением и/или&nbsp;передачей полученной информации
                        по&nbsp;информационно-телекоммуникационным сетям или без таковой.
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <h5>10. Трансграничная передача персональных данных</h5>
                <div class="ol">
                    <div class="li">
                        10.1. Оператор до&nbsp;начала осуществления деятельности
                        по&nbsp;трансграничной передаче персональных данных обязан уведомить
                        уполномоченный орган по&nbsp;защите прав субъектов персональных данных
                        о&nbsp;своем намерении осуществлять трансграничную передачу персональных
                        данных (такое уведомление направляется отдельно от&nbsp;уведомления
                        о&nbsp;намерении осуществлять обработку персональных данных).
                    </div>
                    <div class="li">
                        10.2. Оператор до&nbsp;подачи вышеуказанного уведомления, обязан получить
                        от&nbsp;органов власти иностранного государства, иностранных физических лиц,
                        иностранных юридических лиц, которым планируется трансграничная передача
                        персональных данных, соответствующие сведения.
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <h5>11. Конфиденциальность персональных данных</h5>
                <div class="descr">
                    Оператор и&nbsp;иные лица, получившие доступ к&nbsp;персональным данным, обязаны
                    не&nbsp;раскрывать третьим лицам и&nbsp;не&nbsp;распространять персональные
                    данные без согласия субъекта персональных данных, если иное
                    не&nbsp;предусмотрено федеральным законом.
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <h5>12. Заключительные положения</h5>
                <div class="ol">
                    <div class="li">
                        12.1. Пользователь может получить любые разъяснения по&nbsp;интересующим
                        вопросам, касающимся обработки его персональных данных, обратившись
                        к&nbsp;Оператору с&nbsp;помощью электронной почты <span class="link mark owner-email-field"
                                                                                data-scroll-to="#owner-email-field">support@sputnik-iskozh.ru</span>.
                    </div>
                    <div class="li">
                        12.2. В&nbsp;данном документе будут отражены любые изменения политики
                        обработки персональных данных Оператором. Политика действует бессрочно
                        до&nbsp;замены ее&nbsp;новой версией.
                    </div>
                    <div class="li">
                        12.3. Актуальная версия Политики в&nbsp;свободном доступе расположена
                        в&nbsp;сети Интернет по&nbsp;адресу <span id="owner-privacy-url-value"
                                                                  class="link mark owner-privacy-url-field"
                                                                  data-scroll-to="#owner-privacy-url-field">https://sputnik-iskozh.ru/privacy</span>.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

