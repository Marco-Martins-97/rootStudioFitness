<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Termos e Condições | root — estúdio fitness</title>
  <meta name="description" content="Termos e Condições do estúdio de fitness root." />
  <style>
    :root {
      --bg: #0f0f10;
      --card: #16171a;
      --muted: #a9acb2;
      --text: #f1f3f5;
      --accent: #38d39f; /* vibe root/saúde */
      --danger: #ff6b6b;
      --border: #24262b;
      --link: #7dd3fc;
      --maxw: 960px;
    }
    * { box-sizing: border-box; }
    html, body { height: 100%; }
    body {
      margin: 0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
      background: var(--bg); color: var(--text);
    }
    header {
      border-bottom: 1px solid var(--border);
      background: linear-gradient(180deg, rgba(56,211,159,0.08), transparent 70%);
    }
    .wrap { max-width: var(--maxw); margin: 0 auto; padding: 24px; }
    .brand { display: flex; align-items: center; gap: 12px; }
    .dot { width: 14px; height: 14px; background: var(--accent); border-radius: 999px; box-shadow: 0 0 0 6px rgba(56,211,159,0.15); }
    .brand h1 { margin: 0; font-size: 22px; letter-spacing: 0.6px; text-transform: lowercase; }
    .meta { margin-top: 6px; color: var(--muted); font-size: 14px; }

    nav.toc {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px; padding: 16px; margin: 20px 0 28px;
    }
    nav.toc h2 { margin: 0 0 8px; font-size: 16px; color: var(--muted); font-weight: 600; }
    nav.toc ol { margin: 0; padding-left: 18px; columns: 2; column-gap: 32px; }
    nav.toc a { color: var(--link); text-decoration: none; }
    nav.toc a:hover { text-decoration: underline; }

    main article {
      background: var(--card); border: 1px solid var(--border); border-radius: 18px;
      padding: 20px; line-height: 1.65;
    }
    section { padding: 10px 4px 18px; border-bottom: 1px dashed var(--border); }
    section:last-of-type { border-bottom: 0; }
    h2 { font-size: 20px; margin: 0 0 6px; }
    p, li { color: #dcdfe4; }
    ul, ol { padding-left: 20px; }
    a { color: var(--link); }

    footer {
      color: var(--muted); border-top: 1px solid var(--border); margin-top: 24px; padding: 24px 0 48px;
    }

    .notice { background: rgba(125,211,252,0.08); border: 1px solid #274b63; color: #cfefff; padding: 12px 14px; border-radius: 12px; }
    .danger { background: rgba(255,107,107,0.08); border-color: rgba(255,107,107,0.35); color: #ffd4d4; }

    @media (max-width: 720px) {
      nav.toc ol { columns: 1; }
    }

    /* impressão */
    @media print {
      body { background: #fff; color: #000; }
      header, nav.toc { display: none; }
      main article { border: 0; }
      a { color: #000; text-decoration: none; }
    }
  </style>
</head>
<body>
  <header>
    <div class="wrap">
      <div class="brand" aria-label="root — estúdio fitness">
        <span class="dot" aria-hidden="true"></span>
        <h1>root — termos e condições</h1>
      </div>
      <p class="meta">Última atualização: <time datetime="2025-08-13">13 de agosto de 2025</time></p>
      <p class="notice">Estes Termos regem o uso das instalações, serviços, aulas e plataformas digitais do estúdio fitness <strong>root</strong>. Ao aderir a um plano, reservar uma aula ou utilizar nossas instalações, você concorda integralmente com este documento.</p>
    </div>
  </header>

  <div class="wrap">
    <nav class="toc" aria-label="Índice">
      <h2>Índice</h2>
      <ol>
        <li><a href="#definicoes">1. Definições</a></li>
        <li><a href="#adesao">2. Adesão e elegibilidade</a></li>
        <li><a href="#planos">3. Planos, pagamentos e faturação</a></li>
        <li><a href="#cancelamento">4. Cancelamento, pausa e reembolsos</a></li>
        <li><a href="#aulas">5. Aulas, reservas e presenças</a></li>
        <li><a href="#saude">6. Saúde, riscos e segurança</a></li>
        <li><a href="#instalacoes">7. Uso das instalações</a></li>
        <li><a href="#conduta">8. Conduta e respeito</a></li>
        <li><a href="#imagem">9. Direitos de imagem</a></li>
        <li><a href="#propriedade">10. Propriedade intelectual</a></li>
        <li><a href="#privacidade">11. Privacidade e dados</a></li>
        <li><a href="#comunicacoes">12. Comunicações</a></li>
        <li><a href="#alteracoes">13. Alterações dos Termos</a></li>
        <li><a href="#responsabilidade">14. Limitação de responsabilidade</a></li>
        <li><a href="#indenizacao">15. Indemnização</a></li>
        <li><a href="#lei">16. Lei aplicável e foro</a></li>
        <li><a href="#contactos">17. Contactos</a></li>
      </ol>
    </nav>

    <main>
      <article>
        <section id="definicoes">
          <h2>1. Definições</h2>
          <p>Para efeitos destes Termos: “root” significa o estúdio de fitness operado por <em>[NOME LEGAL DA EMPRESA]</em>, com sede em <em>[MORADA COMPLETA]</em>; “Cliente” ou “Utilizador” é a pessoa física ou jurídica que adere a um plano, compra serviços ou utiliza as instalações; “Plataforma” refere-se aos websites, aplicações e sistemas de reserva da root; “Aulas” incluem treinos, sessões personalizadas e eventos.</p>
        </section>

        <section id="adesao">
          <h2>2. Adesão e elegibilidade</h2>
          <ul>
            <li>Idade mínima de 16 anos; menores devem apresentar autorização expressa do responsável legal e, quando aplicável, acompanhamento profissional.</li>
            <li>Ao aderir, o Cliente declara que leu e aceita estes Termos e que as informações prestadas são verdadeiras e atualizadas.</li>
            <li>Podemos recusar ou cancelar adesões em caso de violação de políticas, riscos à segurança ou conduta imprópria.</li>
          </ul>
        </section>

        <section id="planos">
          <h2>3. Planos, pagamentos e faturação</h2>
          <ul>
            <li>Planos podem ser <strong>mensais, trimestrais, anuais</strong> ou <strong>pacotes de aulas</strong>, conforme descritos na Plataforma ou materiais comerciais.</li>
            <li>Pagamentos são antecipados e não transferíveis. Aceitamos <em>[métodos de pagamento]</em>. Emitimos fatura conforme legislação aplicável.</li>
            <li>Renovações automáticas poderão ocorrer ao final do período, salvo cancelamento prévio pelo Cliente nos prazos definidos.</li>
            <li>Falha no pagamento pode resultar em suspensão de acesso até regularização.</li>
            <li>Taxas (ex.: matrícula, manutenção, no-show) serão claramente informadas antes da cobrança.</li>
          </ul>
        </section>

        <section id="cancelamento">
          <h2>4. Cancelamento, pausa e reembolsos</h2>
          <ul>
            <li>O Cliente pode cancelar o plano pelos canais oficiais. Prazos de aviso prévio: <em>[X dias]</em>.</li>
            <li>Planos pré-pagos não são reembolsáveis, exceto nos termos obrigatórios da lei de consumo aplicável.</li>
            <li>É possível <strong>pausar</strong> o plano por motivos justificados (ex.: saúde, viagem), conforme política de pausa vigente.</li>
            <li>Reservas podem ser canceladas sem penalização até <em>[X horas]</em> antes do início; após esse prazo poderá ser cobrada taxa de <em>no-show</em>.</li>
          </ul>
        </section>

        <section id="aulas">
          <h2>5. Aulas, reservas e presenças</h2>
          <ul>
            <li>As vagas são limitadas e confirmadas mediante reserva na Plataforma.</li>
            <li>Chegue com antecedência. Atrasos superiores a <em>[X minutos]</em> poderão impedir a participação por segurança.</li>
            <li>Instrutores podem ajustar exercícios conforme nível e condições do grupo.</li>
            <li>Eventos especiais poderão ter Termos adicionais.</li>
          </ul>
        </section>

        <section id="saude">
          <h2>6. Saúde, riscos e segurança</h2>
          <p>Atividades físicas envolvem riscos inerentes. Ao participar, o Cliente declara estar apto(a) e isenta a root de responsabilidades por eventos decorrentes de informações de saúde omitidas ou inverídicas.</p>
          <ul>
            <li>Recomendamos avaliação médica prévia e comunicação de condições preexistentes aos nossos profissionais.</li>
            <li>É obrigatório o uso de equipamentos adequados e respeito às orientações de segurança.</li>
            <li>Em caso de mal-estar, interrompa a atividade e informe um colaborador.</li>
          </ul>
          <p class="notice danger"><strong>Aviso:</strong> Não deixe objetos de valor sem vigilância. A root não se responsabiliza por perda, roubo ou danos a pertences pessoais nas instalações, exceto quando exigido por lei.</p>
        </section>

        <section id="instalacoes">
          <h2>7. Uso das instalações</h2>
          <ul>
            <li>Mantenha higiene: utilize toalha, limpe equipamentos após o uso e respeite os horários.</li>
            <li>Guarde pesos e acessórios no devido lugar. Não é permitido fumar ou consumir bebidas alcoólicas.</li>
            <li>Áreas e equipamentos específicos podem ter regras próprias afixadas localmente.</li>
          </ul>
        </section>

        <section id="conduta">
          <h2>8. Conduta e respeito</h2>
          <ul>
            <li>Não toleramos assédio, discriminação ou comportamento abusivo.</li>
            <li>Respeite a privacidade e o espaço de outros Clientes e colaboradores.</li>
            <li>Podemos aplicar advertências, suspensão ou cancelamento em caso de infrações.</li>
          </ul>
        </section>

        <section id="imagem">
          <h2>9. Direitos de imagem</h2>
          <p>Eventos e aulas poderão ser fotografados/filmados para fins institucionais e promocionais. O Cliente pode <strong>optar por não participar</strong> de registos de imagem comunicando previamente na receção ou por e-mail. Conteúdos captados pela root permanecem de sua propriedade.</p>
        </section>

        <section id="propriedade">
          <h2>10. Propriedade intelectual</h2>
          <p>Marcas, programas de treino, textos, fotografias, vídeos e materiais disponibilizados pela root são protegidos por direitos de autor e demais normas. É proibida a reprodução, distribuição ou uso comercial sem autorização escrita.</p>
        </section>

        <section id="privacidade">
          <h2>11. Privacidade e dados</h2>
          <p>Tratamos dados pessoais conforme a legislação aplicável (ex.: RGPD). As finalidades incluem gestão de planos, reservas, faturação, segurança e comunicações. O Cliente pode exercer direitos de acesso, retificação, eliminação e portabilidade através dos nossos canais.</p>
          <ul>
            <li>Conservamos dados pelo tempo necessário ao cumprimento das finalidades ou exigências legais.</li>
            <li>Partilhamos dados apenas com fornecedores essenciais (ex.: processamento de pagamentos), sob acordos adequados.</li>
            <li>Consulte a <a href="#" aria-label="Política de Privacidade">Política de Privacidade</a> para detalhes.</li>
          </ul>
        </section>

        <section id="comunicacoes">
          <h2>12. Comunicações</h2>
          <p>Comunicamos por e-mail, SMS, telefone e notificações da Plataforma. O Cliente pode gerir preferências de marketing a qualquer momento. Mensagens operacionais (ex.: alterações de aula) são essenciais e não podem ser desativadas.</p>
        </section>

        <section id="alteracoes">
          <h2>13. Alterações dos Termos</h2>
          <p>Poderemos atualizar estes Termos para refletir mudanças legais, operacionais ou de serviços. As alterações entram em vigor na data indicada no topo. Manteremos versão atual na Plataforma e, quando relevante, notificaremos Clientes ativos.</p>
        </section>

        <section id="responsabilidade">
          <h2>14. Limitação de responsabilidade</h2>
          <ul>
            <li>Na máxima extensão permitida por lei, a root não responde por danos indiretos, incidentais, especiais ou lucros cessantes decorrentes do uso dos serviços.</li>
            <li>Quando a responsabilidade não puder ser excluída, será limitada ao valor pago pelo Cliente nos <em>[últimos X meses]</em>.</li>
            <li>Eventos de <em>força maior</em> (ex.: desastres, greves, pandemias, falhas de serviços de terceiros) podem afetar temporariamente a prestação, sem caracterizar incumprimento.</li>
          </ul>
        </section>

        <section id="indenizacao">
          <h2>15. Indemnização</h2>
          <p>O Cliente concorda em indemnizar e manter indene a root, seus colaboradores e parceiros contra reclamações decorrentes de violação destes Termos, uso indevido das instalações ou infração de direitos de terceiros.</p>
        </section>

        <section id="lei">
          <h2>16. Lei aplicável e foro</h2>
          <p>Estes Termos são regidos pelas leis de <em>[PAÍS/REGIÃO]</em>. Fica eleito o foro de <em>[CIDADE]</em>, com renúncia a qualquer outro, para dirimir controvérsias que não possam ser resolvidas amigavelmente.</p>
        </section>

        <section id="contactos">
          <h2>17. Contactos</h2>
          <address>
            <p><strong>root — estúdio fitness</strong><br/>
            <span>[MORADA COMPLETA]</span><br/>
            <a href="mailto:contacto@root.fit">contacto@root.fit</a> · <a href="tel:+351000000000">+351 000 000 000</a><br/>
            <span>NIF/NIPC: [NÚMERO]</span></p>
          </address>
        </section>
      </article>
    </main>

    <footer>
      <p>© <span id="y"></span> root. Todos os direitos reservados.</p>
    </footer>
  </div>

  <script>
    // Atualiza o ano no rodapé automaticamente
    document.getElementById('y').textContent = new Date().getFullYear();
  </script>
</body>
</html>