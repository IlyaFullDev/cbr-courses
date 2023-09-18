<!DOCTYPE html>
<html lang="ru"  data-bs-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
        
        <title>CBR.ru Курсы</title>

    </head>
    <body class="container">
        <h1 class="mb-5">Курсы cbr.ru</h1>

        <form action="{{ route('curs-form') }}" method="post">

            @csrf

            <div class="form-group my-3">
                <label for="date" class="d-block p-2 bg-body-secondary mb-2">Дата</label>
                <input type="date" id="date" name="date" value="{{ date('Y-m-d', strtotime('now')) }}" class="form-control"/>
            </div>

            <div class="form-group my-3">
                <label for="vfrom"  class="d-block p-2 bg-body-secondary mb-2">Валюта отдаём</label>
                <select name="vfrom" id="vfrom" class="form-select form-select-lg mb-3">
                    <option value="USD" selected>USD (Доллар США)</option>
                    <option value="RUB">RUB (Российский рубль)</option>
                    <option value="EUR">EUR (Евро)</option>
                    <option value="GBP">GBP (Фунт стерлингов Соединенного королевства)</option>
                    <option value="BYN">BYN (Белорусский рубль)</option>
                    <option value="AUD">AUD (Австралийский доллар)</option>
                    <option value="AZN">AZN (Азербайджанский манат)</option>
                    <option value="BRL">BRL (Бразильский реал)</option>
                    <option value="KZT">KZT (Казахстанских тенге)</option>
                    <option value="CNY">CNY (Китайский юань)</option>
                    <option value="PLN">PLN (Польский злотый)</option>
                </select>
            </div>
                
            <div class="form-group my-3">
                <label for="vto"  class="d-block p-2 bg-body-secondary mb-2">Валюта получаем</label>
                <select name="vto" id="vto" class="form-select form-select-lg mb-3">
                    <option value="RUB" selected>RUB (Российский рубль)</option>
                    <option value="USD">USD (Доллар США)</option>
                    <option value="EUR">EUR (Евро)</option>
                    <option value="GBP">GBP (Фунт стерлингов Соединенного королевства)</option>
                    <option value="BYN">BYN (Белорусский рубль)</option>
                    <option value="AUD">AUD (Австралийский доллар)</option>
                    <option value="AZN">AZN (Азербайджанский манат)</option>
                    <option value="BRL">BRL (Бразильский реал)</option>
                    <option value="KZT">KZT (Казахстанских тенге)</option>
                    <option value="CNY">CNY (Китайский юань)</option>
                    <option value="PLN">PLN (Польский злотый)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success mt-3">Отправить</button>
        </form>

        @if($errors->any())
            <div class="alert alert-danger mt-5">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(isset($data))
            <div class="bg-body-secondary mt-5 p-3">
                <h2>Курс валюты</h2>
                <p>Курс на выбранный день: 
                    <span class="fs-3 px-4 py-1 border bg-secondary-subtle mx-2
                        @if($data['difference'] > 0)
                            text-success
                        @elseif($data['difference'] < 0)
                            text-danger
                        @else
                            text-body-tertiary
                        @endif
                        ">
                        {{ $data['curs_today'] }}  {{ $data['curr_to'] }}
                    </span>
                    за {{ $data['nominal'] }} {{ $data['curr_from'] }}
                </p>

                <p>Курс за предыдущий день:   
                    <span class="fs-3 px-4 py-1 border bg-secondary-subtle mx-2">{{ $data['curs_yesterday'] }}  {{ $data['curr_to'] }}</span>
                    за {{ $data['nominal'] }} {{ $data['curr_from'] }}
                </p>

                <p>Разница курсов:      <span class="fs-3 px-4 py-1 border bg-secondary-subtle mx-2">{{ $data['difference'] }}  {{ $data['curr_to'] }}</span></p>
            </div>
        @endif

        <div class="bg-body-secondary mt-5 p-3">
                <h2>Добавить очередь</h2>
                <p>Добавить очередь парсинга курсов за 180 дней (1 задание - 1 день)</p>
                <p>
                    <a href="/queue/" class="btn btn-success mt-3">Старт</a>
                </p>
        </div>

    </body>
</html>
