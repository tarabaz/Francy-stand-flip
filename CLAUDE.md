# Note di progetto — Francy-stand-flip

Plugin WordPress `francy-stand-flip/` per FrancyStore3D (vedi README.md per uso e shortcode).

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

Le variabili CSS inline sono **numeri senza unità**; le unità si applicano nel CSS
moltiplicando per `--fsf-u` (`1px` di fallback, `100cqw / larghezza-box` con container query).
Chi aggiunge una nuova misura deve:

1. esportare il numero in `FSF_Render::box_vars()` con suffisso `-n`;
2. usarlo in `assets/css/front.css` come `calc(var(--fsf-x-n) * var(--fsf-u))`;
3. replicare la stessa logica in `buildVars()` di `assets/js/admin.js`, altrimenti l'anteprima
   live si scosta dal front-end.

Le scelte non numeriche (unità, lato immagine, on/off delle ombre) passano da classi generate
in `FSF_Render::box_classes()` e replicate in `buildClasses()` nel JS.

## Verifica senza WordPress

Non c'è WordPress in questo ambiente. Per controllare il layout si può renderizzare l'HTML
reale con degli stub delle funzioni WP e fotografarlo con Chromium/Playwright
(`/opt/pw-browsers/chromium`): stub di `__`, `esc_*`, `wp_parse_args`, `get_option`,
`sanitize_html_class`, poi `FSF_Render::box( 0, $settings, array( 'preview' => true ) )`.
Backend, salvataggio meta e anteprima live restano da provare su installazione vera.
