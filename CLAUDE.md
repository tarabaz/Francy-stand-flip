# Note di progetto — Francy-stand-flip

Plugin WordPress per FrancyStore3D (vedi README.md per uso e shortcode). **I file del plugin
stanno nella radice del repository**: serve perché lo zip scaricato da GitHub sia installabile
direttamente in WordPress. Non rimetterli in una sottocartella.

Lo zip pubblicato nella release `latest` viene ricostruito da `.github/workflows/zip-plugin.yml`
ad ogni push su `main`.

## Git — flusso richiesto da Valerio

Al termine di ogni lavoro, **aggiornare sempre `main` su GitHub**, non solo il branch di
sviluppo:

```
git push -u origin <branch-di-lavoro>
git checkout main && git merge --ff-only <branch-di-lavoro>
git push -u origin main
git checkout <branch-di-lavoro>
```

Se il fast-forward non è possibile (main è avanti), fare un merge normale e risolvere i
conflitti, mai un force-push su `main`.

## Convenzioni del codice

- Prefissi: classi `FSF_`, funzioni `fsf_`, meta `_fsf_`, costanti `FSF_`, CSS `.fsf-`,
  variabili CSS `--fsf-`.
- Un file per classe in `includes/`, caricato da `francy-stand-flip.php`.
- Testi dell'interfaccia in italiano, con `__()` e text domain `francy-stand-flip`.
- Commenti e messaggi di commit in italiano.
- Escaping obbligatorio in output (`esc_html`, `esc_attr`, `esc_url`) e sanitizzazione in
  input (vedi `FSF_Settings::sanitize()` e `FSF_Metabox::save()`).

## Come sono costruite le misure del box

I livelli del box sono, dal basso: `.fsf-box__inner` (sfondo), `.fsf-card`, `.fsf-stand`,
`.fsf-content`. La carta sta **dietro** la foto dello stand: è una richiesta esplicita di Valerio,
il prodotto è la foto. Sono tutti figli diretti di `.fsf-box`, non annidati, proprio per poterli
impilare in quest'ordine.

Le variabili CSS inline sono **numeri senza unità**; le unità si applicano nel CSS
moltiplicando per `--fsf-u` (`1px` di fallback, `100cqw / larghezza-box` con container query).
Chi aggiunge una nuova misura deve:

1. esportare il numero in `FSF_Render::box_vars()` con suffisso `-n`;
2. usarlo in `assets/css/front.css` come `calc(var(--fsf-x-n) * var(--fsf-u))`;
3. replicare la stessa logica in `buildVars()` di `assets/js/admin.js`, altrimenti l'anteprima
   live si scosta dal front-end.

Le scelte non numeriche (unità, lato immagine, on/off delle ombre, foto libera, larghezza
dinamica, stile del pulsante) passano da classi generate in `FSF_Render::box_classes()` e
replicate in `buildClasses()` nel JS.

Con la **larghezza dinamica** `--fsf-u` torna a `1px` e si usano le variabili `*-px-n` calcolate
da PHP: chi aggiunge una misura che deve restare fissa in quella modalità deve esportare anche la
sua versione in px.

Il titolo-didascalia (`.fsf-title--below`) sta **fuori** da `.fsf-box`, dentro `.fsf-item`: lì
`--fsf-u` non esiste, quindi usa px fissi e il suo colore dedicato. In anteprima entrambi i titoli
vengono renderizzati e il JS nasconde quello inattivo.

Avada sovrascrive colore e margini di `h3` e dei link: titolo, descrizione e pulsanti usano
selettori rinforzati (`.fsf-box .fsf-content .fsf-title`) e `!important` sulle sole proprietà di
colore. Non abbassare quella specificità.

## Verifica senza WordPress

Non c'è WordPress in questo ambiente. Per controllare il layout si può renderizzare l'HTML
reale con degli stub delle funzioni WP e fotografarlo con Chromium/Playwright
(`/opt/pw-browsers/chromium`): stub di `__`, `esc_*`, `wp_parse_args`, `get_option`,
`sanitize_html_class`, poi `FSF_Render::box( 0, $settings, array( 'preview' => true ) )`.
Backend, salvataggio meta e anteprima live restano da provare su installazione vera.
