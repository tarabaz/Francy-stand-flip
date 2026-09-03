# Francy Stand Flip

Plugin WordPress per FrancyStore3D: mostra gli stand in un **box a dimensione fissa** con la
carta Pokémon che sborda dall'angolo in alto a sinistra, la foto dello stand sul lato opposto,
titolo, breve descrizione e due link Instagram (post e reel).

Si inserisce in qualsiasi pagina — Avada compreso — tramite shortcode.

![Struttura del box](docs/anteprima.png)

---

## Installazione

1. Comprimi la cartella `francy-stand-flip/` in uno zip (dentro lo zip deve esserci la cartella,
   non i file sfusi).
2. WordPress → Plugin → Aggiungi nuovo → Carica plugin → scegli lo zip → Installa → Attiva.
3. In bacheca compare il menu **Stand Flip**.

Per aggiornare: sovrascrivi la cartella via FTP oppure reinstalla lo zip. Le impostazioni e gli
stand restano (sono in opzioni e post, non nei file).

---

## Come si usa

### 1. Creare uno stand

**Stand Flip → Aggiungi stand**. Ogni stand è un "post" con:

| Campo | Note |
|---|---|
| Titolo | è il titolo mostrato nel box |
| Immagine carta Pokémon | PNG con trasparenza consigliato: è quella che sborda |
| Foto dello stand | se la lasci vuota viene usata l'immagine in evidenza |
| Breve descrizione | tenuta corta e troncata a N righe (impostazione globale) per non sballare il box |
| Link al post Instagram | pulsante principale |
| Link al reel Instagram | opzionale: se vuoto il pulsante non appare |
| Aspetto di questo box | override facoltativi di posizione/rotazione/larghezza carta e colore di sfondo |
| Gruppi | tassonomia facoltativa per filtrare la griglia (es. `fiere-2025`) |
| Ordine (Attributi pagina) | numero d'ordine usato dalla griglia |

Nella colonna di destra trovi lo **shortcode pronto da copiare**; lo stesso lo vedi nella lista
di tutti gli stand.

### 2. Inserire i box in una pagina Avada

Nel Fusion Builder aggiungi un elemento **Code Block** (o "Testo") e incolla lo shortcode.

Griglia con tutti gli stand:

```
[francy_stands]
```

Singolo stand (uno shortcode per ogni stand):

```
[francy_stand id="123"]
```

### 3. Attributi degli shortcode

`[francy_stands]`

| Attributo | Default | Cosa fa |
|---|---|---|
| `columns` | da impostazioni | colonne su desktop (1–6) |
| `gap` | da impostazioni | spazio tra i box in px |
| `ids` | — | solo questi stand, in quest'ordine: `ids="12,45,9"` |
| `exclude` | — | esclude questi ID |
| `group` | — | filtra per slug del gruppo: `group="fiere-2025"` |
| `limit` | tutti | numero massimo di box |
| `orderby` | `menu_order` | `menu_order`, `date`, `title`, `modified`, `rand` |
| `order` | `ASC` | `ASC` o `DESC` |
| `class` | — | classi CSS extra sul contenitore |

`[francy_stand]`

| Attributo | Cosa fa |
|---|---|
| `id` | ID dello stand |
| `slug` | alternativa all'ID |
| `class` | classi CSS extra |

Esempi:

```
[francy_stands columns="3" gap="56"]
[francy_stands group="fiere-2025" orderby="date" order="DESC"]
[francy_stand slug="stand-pikachu"]
```

---

## Impostazioni globali

**Stand Flip → Impostazioni**, con **anteprima live** a lato (si aggiorna mentre muovi i valori,
poi salvi).

- **Box**: larghezza massima, altezza (usata come proporzione), arrotondamento angoli, sfondo
  pieno o sfumato con angolo, bordo, ombra del box.
- **Carta Pokémon**: coordinate X/Y relative al box (in % o px, valori negativi per far sbordare),
  larghezza, rotazione in gradi, arrotondamento, ombra dedicata con offset/sfocatura/colore/opacità.
- **Immagine stand**: lato (destra/sinistra), larghezza dell'area, `cover`/`contain`, messa a
  fuoco, margine interno, sfumatura di raccordo verso i testi.
- **Testi**: padding, larghezza colonna testi, allineamento verticale, dimensioni e colori di
  titolo e descrizione, numero massimo di righe della descrizione.
- **Pulsanti**: dimensione, arrotondamento, colori, icona Instagram on/off, etichette, target link.
- **Griglia**: colonne desktop/tablet/mobile, gap, margine automatico per la carta che sborda.

Ogni singolo stand può sovrascrivere posizione/rotazione/larghezza della carta e i colori di
sfondo: comodo quando una carta ha proporzioni diverse o quando vuoi il fondo abbinato ai colori
di quella carta.

---

## Note tecniche

### Il box resta sempre uguale

Il box ha una proporzione fissa (`aspect-ratio`) e tutto il contenuto è espresso in multipli di
`--fsf-u`, che vale `100cqw / larghezza-box`: testi, padding, ombre e carta scalano insieme al box,
quindi il layout è identico a qualsiasi larghezza. La descrizione è troncata al numero di righe
impostato, così testi più lunghi non allungano il box.

Sotto i ~430px di larghezza del box la divisione testo/immagine non regge: il box passa
automaticamente a layout **impilato** (foto sopra, testi sotto, altezza automatica) con dimensioni
minime di sicurezza per i testi.

### La carta che sborda

La carta è in `position: absolute` dentro il box, con coordinate relative al box e `overflow`
visibile. Il contenitore della griglia riceve un **margine automatico** calcolato da posizione,
larghezza e rotazione della carta, così la parte che esce non viene mai tagliata.

Se in Avada la carta risulta comunque tagliata: nelle impostazioni della colonna/contenitore che
ospita lo shortcode metti **Overflow: visible** (il plugin prova già a forzarlo via CSS).

### Immagini consigliate

- Carta: PNG con trasparenza, lato lungo ~840px (proporzione carta 63×88mm ≈ 1:1,4).
- Foto stand: JPG 1600×1200 circa, soggetto centrato o leggermente sul lato dove viene mostrata.
- Il plugin usa `srcset` di WordPress e `loading="lazy"`, quindi non serve caricare file enormi.

### Struttura file

```
francy-stand-flip/
├── francy-stand-flip.php        bootstrap, costanti, attivazione
├── includes/
│   ├── class-fsf-cpt.php        custom post type "Stand" + tassonomia "Gruppi"
│   ├── class-fsf-settings.php   impostazioni globali, campi, sanitizzazione, pagina admin
│   ├── class-fsf-metabox.php    campi del singolo stand + override + anteprima
│   ├── class-fsf-render.php     variabili CSS e markup del box e della griglia
│   ├── class-fsf-shortcodes.php [francy_stands] e [francy_stand]
│   └── class-fsf-admin.php      asset backend, colonne lista, link impostazioni
└── assets/
    ├── css/front.css            CSS del box (unico file caricato sul sito)
    ├── css/admin.css            CSS backend
    ├── js/admin.js              anteprima live, selettore immagini, color picker
    └── img/                     segnaposto usati solo nell'anteprima
```

Il CSS del front-end viene caricato solo dove serve (shortcode presente nel contenuto o
renderizzato da un builder).
