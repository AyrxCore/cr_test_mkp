<template>
  <div
    :class="{
      'relative mb-3 flex w-2/3 justify-center border p-2 font-bold': !page,
      'home-title my-10 ': page,
    }"
  >
    CONDITIONS GÉNÉRALES D'UTILISATION
    <br v-if="!page" />
    DE LA MARKETPLACE « QANTIS MARKET »
  </div>
  <div class="pt-3 pb-6 text-sm md:text-base lg:text-lg">
    <p class="italic">Entrée en vigueur le 06 mars 2023</p>
    <p class="mt-5 uppercase">
      IL EST IMPORTANT QUE VOUS LISIEZ ATTENTIVEMENT LES PRESENTES CONDITIONS
      GENERALES AVANT D'UTILISER CETTE MARKETPLACE.
    </p>
    <p class="mt-5 uppercase">
      SI VOUS N'ACCEPTEZ PAS LES TERMES DESDITES CONDITIONS GENERALES, vous ne
      devez pas CLIQUER SUR « J'ACCEPTE ». LA SOCIETE « QANTIS » vous invite à
      imprimer LES PRESENTES avant de donner votre acceptation, afin de vous
      assurer que vous les acceptez sans réserve.
    </p>
    <p class="mt-5 uppercase">
      SI VOUS UTILISEZ DE QUELQUE MANIERE QUE CE SOIT CETTE MARKETPLACE, VOUS
      VOUS ENGAGEZ A RESPECTER LES TERMES DES PRESENTES CONDITIONS GENERALES QUI
      CONSTITUENT UN CONTRAT ENTRE LA SOCIETE « QANTIS » ET VOUS-MEME (CI-APRES
      « LE <span class="font-bold">CONTRAT</span> »).
    </p>
    <p class="mt-5 font-bold">1 - <span class="underline">Définitions</span></p>
    <p class="mt-5">
      Les termes définis au présent article et utilisés dans les présentes
      conditions générales, au singulier comme au pluriel, auront la
      signification suivante:
    </p>
    <ul class="mt-5 list-none space-y-6">
      <li>
        « <span class="font-bold">Adhérent</span> » : désigne toute entité ayant
        conclu une convention d'adhésion avec QANTIS en vue notamment de pouvoir
        bénéficier des Services.
      </li>
      <li>
        « <span class="font-bold">CGU</span> » : désigne les présentes
        Conditions Générales d'Utilisation.
      </li>
      <li>
        « <span class="font-bold">Fournisseur</span> » : désigne toute entité
        ayant conclu un contrat-cadre de référencement avec QANTIS en vue
        notamment d'être référencé sur la Marketplace et de pouvoir bénéficier
        des Services.
      </li>
      <li>
        « <span class="font-bold">Licence</span> » : désigne le droit
        d'utilisation de la Marketplace permettant à tout Utilisateur d'y
        accéder et de l'utiliser pendant la durée pour laquelle il est octroyé.
      </li>
      <li>
        « <span class="font-bold">Marketplace</span> » : désigne la Marketplace
        d'intermédiation « QANTIS MARKET » développée par QANTIS, mise à
        disposition des Utilisateurs depuis l'url suivante:
        <a href="https://marketplace.qantis.co"
          >https://marketplace.qantis.co</a
        >
      </li>
      <li>
        « <span class="font-bold">Offre</span> » : désigne toute offre de
        Produit ou de Prestation proposée à la vente par le Fournisseur depuis
        la Marketplace.
      </li>
      <li>
        « <span class="font-bold">Page Fournisseur</span> » : désigne la page
        web dédiée au Fournisseur sur la Marketplace, celle-ci étant créée par
        QANTIS puis gérée et administrée par le Fournisseur dans le cadre des
        Services qui lui sont mis à disposition.
      </li>
      <li>
        « <span class="font-bold">Partie</span> » ou «
        <span class="font-bold">Parties</span> » : désignent QANTIS et
        l'Utilisateur, individuellement ou collectivement.
      </li>
      <li>
        « <span class="font-bold">Prestation</span> » : désigne toute prestation
        référencée par la Marketplace en vue d'être proposée à la vente par un
        Fournisseur.
      </li>
      <li>
        « <span class="font-bold">Produit</span> » : désigne tout produit
        référencé par la Marketplace en vue d'être proposé à la vente par un
        Fournisseur.
      </li>
      <li>
        « <span class="font-bold">QANTIS</span> » : désigne la société qui a
        développé la Marketplace : QANTIS, SAS au capital de 100.000,00 euros,
        dont le siège social est situé 185 ALLEE DES CYPRES 69760 LIMONEST,
        immatriculée au RCS de LYON sous le numéro 538 492 380.
      </li>
      <li>
        « <span class="font-bold">Services</span> » : désigne les services
        proposés par la Marketplace tels que décrits aux présentes.
      </li>
      <li>
        « <span class="font-bold">Utilisateur</span> » : désigne tout
        professionnel personne physique bénéficiant d'un droit d'utilisation de
        la Marketplace selon les termes des présentes CGU, à savoir :
        <ul class="ml-5 list-none">
          <li>
            - « <span class="font-bold">Utilisateur Adhérent</span> » : désigne
            tout Utilisateur membre d'un Adhérent;
          </li>
          <li>
            - « <span class="font-bold">Utilisateur Fournisseur</span> » :
            désigne tout Utilisateur membre d'un Fournisseur.
          </li>
        </ul>
      </li>
    </ul>
    <p class="mt-5 font-bold">
      2 - <span class="underline">Champ d'application</span>
    </p>
    <p class="mt-5">
      <span class="font-bold">2.1</span> Les présentes CGU définissent les
      modalités et les conditions dans lesquelles l'Utilisateur accède aux
      Services et en bénéficie.
    </p>
    <p class="mt-5">
      <span class="font-bold">2.2</span> Toute utilisation de la Marketplace par
      l'Utilisateur implique l'acceptation pleine et sans réserve des présentes
      CGU. L'Utilisateur agrée les présentes conditions générales, reconnaît en
      avoir parfaite connaissance et renonce de ce fait à se prévaloir de tout
      document contradictoire.
    </p>
    <p class="mt-5">
      <span class="font-bold">2.3</span> La relation entre les Parties sera
      toujours régie par les dernières conditions en vigueur au jour de
      l'utilisation par l'Utilisateur de la Marketplace. En cas de modification
      des présentes CGU, l'Utilisateur sera invité à accepter les nouvelles
      conditions, qui lui seront applicables au jour de l'acceptation.
    </p>
    <p class="mt-5 font-bold">
      3 - <span class="underline">Description de la Marketplace</span>
    </p>
    <p class="mt-5">
      La Marketplace a pour objet de faciliter la mise en relation entre des
      Adhérents et des Fournisseurs en vue de l'acquisition par les Adhérents de
      Produits et Prestations vendus par les Fournisseurs, à des tarifs
      préférentiels négociés par QANTIS.
    </p>
    <p class="mt-5">
      Elle permet aux Fournisseurs d'être référencés sur la Marketplace et de
      gérer la commercialisation de leurs Produits et Prestations via la
      Marketplace, à travers la Page Fournisseur qui leur est dédiée.
    </p>
    <p class="mt-5">
      Elle permet aux Adhérents de parcourir les offres de Produits et
      Prestations susceptibles de les intéresser et de procéder à l'achat de
      Produits et Prestations via la Marketplace, à des conditions tarifaires
      avantageuses.
    </p>
    <p class="mt-5">
      La Marketplace permet ainsi une mise en relation entre des professionnels
      pouvant, à terme, déboucher sur la conclusion d'un contrat de vente entre
      ces derniers.
    </p>
    <p class="mt-5 font-bold">
      4 - <span class="underline">Accès à la Marketplace</span>
    </p>
    <p class="mt-5">
      <span class="font-bold">4.1</span> L'accès à la Marketplace est restreint
      aux seuls Utilisateurs agissant dans le cadre de leur activité
      professionnelle et bénéficiant d'un droit d'accès en vertu d'un contrat
      conclu par les Adhérents et les Fournisseurs avec QANTIS.
    </p>
    <p class="mt-5">
      Pour accéder à la Marketplace et l'utiliser, l'Utilisateur doit posséder
      un ordinateur, un terminal mobile ou une tablette numérique compatible
      ainsi qu'un accès au réseau Internet.
    </p>
    <p class="mt-5">
      L'Utilisateur est informé que l'utilisation de la Marketplace entraîne
      l'application des présentes CGU.
    </p>
    <p class="mt-5"><span class="font-bold">4.2</span> Accès adhérent</p>
    <p class="mt-5">
      L'accès à la Marketplace par les Utilisateurs Adhérents s'effectue via les
      autres Marketplaces QANTIS existantes.
    </p>
    <p class="mt-5">
      Ainsi, l'Utilisateur Adhérent qui souhaite se crée un compte sur la
      Marketplace se connecte grâce à son adresse email professionnelle. Si
      cette adresse email est reconnue par QANTIS, l'Utilisateur Adhérent reçoit
      par email un code de connexion à 4 chiffres qu'il est invité à renseigner
      à sa première connexion sur la Marketplace pour pouvoir se connecter.
    </p>
    <p class="mt-5">
      L'Utilisateur doit également accepter les présentes CGU en cochant la case
      prévue à cet effet.
    </p>
    <p class="mt-5"><span class="font-bold">4.3</span> Accès fournisseur</p>
    <p class="mt-5">
      Pour pouvoir accéder à la Marketplace, l'Utilisateur Fournisseur doit en
      faire la demande à QANTIS, qui ouvrira un compte à l'Utilisateur
      directement depuis le back-office de la Marketplace.
    </p>
    <p class="mt-5">
      <span class="font-bold">4.4</span> Il est offert à l'Utilisateur un accès
      réservé et sécurisé à son compte via son identifiant et son mot de passe.
      L'identifiant de connexion de l'Utilisateur est son adresse email.
    </p>
    <p class="mt-5">
      L'Utilisateur souhaitant créer son compte est invité à choisir un mot de
      passe sûr contenant au moins 12 caractères.
    </p>
    <p class="mt-5">
      L'Utilisateur est responsable du mot de passe qu'il choisit au moment de
      son inscription et s'engage à en être le seul détenteur. QANTIS ne pourra
      en aucun cas être tenue responsable des conséquences d'une utilisation
      frauduleuse du mot de passe par un tiers.
    </p>
    <p class="mt-5 font-bold">
      5 -
      <span class="underline">Services proposés par la Marketplace</span>
    </p>
    <p class="mt-5 font-bold">
      5.1 En accédant à la Marketplace via son compte, l'Utilisateur peut
      bénéficier des Services.
    </p>
    <p class="mt-5">
      Ces Services varient selon que l'Utilisateur est un Utilisateur Adhérent
      ou un Utilisateur Fournisseur.
    </p>
    <p class="mt-5">
      Au jour de l'acceptation des présentes CGU, les Services accessibles sur
      la Marketplace par l'Utilisateur Adhérent sont les suivants :
    </p>
    <ul class="ml-5 mt-3">
      <li>- Possibilité de consulter les Offres sur la Marketplace,</li>
      <li>
        - Possibilité de passer des commandes d'Offres en ligne depuis la
        Marketplace,
      </li>
      <li>
        - Possibilité de suivre ses commandes et de retrouver ses factures
        depuis la Marketplace.
      </li>
    </ul>
    <p class="mt-5">
      Au jour de l'acceptation des présentes CGU, les Services accessibles sur
      la Marketplace par l'Utilisateur Fournisseur sont les suivants
    </p>
    <ul class="ml-5 mt-3">
      <li>- Possibilité de publier des Offres sur la Marketplace,</li>
      <li>- Possibilité de gérer et administrer sa Page Fournisseur,</li>
      <li>- Possibilité d'accéder au back-office de sa Page Fournisseur,</li>
      <li>
        - Possibilité de vendre ses Offres directement depuis la Marketplace,
      </li>
      <li>- Possibilité de gérer sa facturation depuis la Marketplace.</li>
    </ul>
    <p class="mt-5">
      <span class="font-bold">5.2</span> L'Utilisateur reconnaît que la
      Marketplace est susceptible d'évoluer et qu'à ce titre, les Services
      pourraient être adaptés en conséquence.
    </p>
    <p class="mt-5">
      Toute suppression et toute modification substantielle de Services proposés
      sur la Marketplace sera notifiée à l'Utilisateur par email et/ou par
      bandeau d'information sur la Marketplace.
    </p>
    <p class="mt-5">
      <span class="font-bold">5.3</span> L'utilisation de la Marketplace pourra
      entraîner la consommation d'une partie du forfait de transmission de
      données souscrit auprès de son opérateur mobile et/ou internet par
      l'Utilisateur. Toute utilisation de la Marketplace depuis l'étranger est
      susceptible d'entraîner des coûts considérablement plus élevés que ceux
      d'une utilisation depuis la France. L'obtention des informations
      correspondantes et le paiement d'éventuels frais, notamment d'itinérance,
      facturés par l'opérateur de l'Utilisateur, lui incombent entièrement.
    </p>
    <p class="mt-5 font-bold">
      6 - <span class="underline">Fonctionnement de la Marketplace</span>
    </p>
    <p class="mt-5 font-bold">
      6.1 Publication d'une Offre par l'Utilisateur Fournisseur
    </p>
    <p class="mt-5">
      Dans le cadre des Services qui lui sont mis à disposition, l'Utilisateur
      Fournisseur dispose de la faculté de télécharger sur la Marketplace son
      catalogue de Produits et Prestations, directement depuis sa Page
      Fournisseur.
    </p>
    <p class="mt-5">
      Il a ainsi la possibilité de publier une Offre qu'il souhaite proposer à
      la vente sur la Marketplace, sous réserve des dispositions des articles
      6.2 et 6.3 ci-après
    </p>
    <p class="mt-5">
      L'Utilisateur Fournisseur est libre d'administrer sa Page Fournisseur
      comme il le souhaite, sous réserve du respect des présentes CGU.
    </p>
    <p class="mt-5 font-bold">6.2 Modération de l'Offre par QANTIS</p>
    <p class="mt-5">
      La mise en ligne d'une Offre sur la Marketplace n'interviendra qu'après
      validation des imports par les équipes de QANTIS, qui s'assureront du
      respect du contenu de l'Offre aux présentes CGU.
    </p>
    <p class="mt-5">
      Ainsi, l'Utilisateur Fournisseur est informé qu'une Offre n'apparaîtra pas
      en ligne sur la Marketplace tant que celle-ci n'aura pas été validée par
      QANTIS.
    </p>
    <p class="mt-5">
      Au terme de ces vérifications, QANTIS pourra valider l'Offre ou exiger de
      l'Utilisateur Fournisseur qu'il la modifie ou la complète. QANTIS pourra
      également refuser la publication de l'Offre de façon motivée.
    </p>
    <p class="mt-5">
      L'Utilisateur Fournisseur ayant publié l'Offre sera informé par email de
      la validation ou du refus de publication de l'Offre.
    </p>
    <p class="mt-5">
      En tout état de cause, QANTIS ne saurait être tenue responsable si, malgré
      les vérifications auxquelles elle procède, une Offre publiée sur la
      Marketplace contreviendrait aux dispositions des présentes CGU ou à la
      règlementation en vigueur.
    </p>
    <p class="mt-5 font-bold">
      6.3 Dispositions spécifiques applicables aux Produits relevant du principe
      de responsabilité élargie du producteur (REP)
    </p>
    <p class="mt-5 text-sm italic">6.3.1 Dispositions générales</p>
    <p class="mt-5">
      La publication par l'Utilisateur Fournisseur sur la Marketplace d'une
      Offre portant sur des Produits relevant du principe de responsabilité
      élargie du producteur en vertu de l'article L.541-10 du Code de
      l'environnement est soumise au respect du présent article.
    </p>
    <p class="mt-5">
      Le Fournisseur reste seul responsable de la détermination des Produits
      soumis au principe de responsabilité élargie du producteur et de
      l'exécution des obligations qui lui incombent au titre des articles
      L.541-10 et/ou L.541-10-8 du Code de l'environnement relatif à la
      contribution à la gestion et la prévention des déchets et la reprise des
      produits usagés auprès de l'utilisateur final.
    </p>
    <p class="mt-5 text-sm italic">
      6.3.2 Communication des identifiants uniques (IDU)
    </p>
    <p class="mt-5">
      L'Utilisateur Fournisseur garantit avoir adhéré à l'un ou plusieurs des
      éco-organismes agréés pour la gestion et la prévention des déchets générés
      par la vente des Produits, et avoir obtenu l'identifiant unique (IDU)
      correspondant prévu à l'article L.541-10-13 du Code de l'environnement.
      Lorsque l'Utilisateur Fournisseur n'est pas le producteur des Produits au
      sens des dispositions du Code de l'environnement, et en particulier de
      l'article L.541-10 du Code de l'environnement, l'Utilisateur Fournisseur
      se porte fort du respect de ces obligations par le producteur des Produits
      visés dans l'Offre.
    </p>
    <p class="mt-5">
      L'Utilisateur Fournisseur qui publie une Offre portant sur des Produits
      relevant du principe de responsabilité élargie du producteur est tenu de
      fournir à QANTIS, au plus tard lors de la création de l'Offre, le ou les
      identifiants uniques (IDU) valides associés aux Produits. Cette obligation
      s'applique y compris lorsque l'Utilisateur Fournisseur n'est pas le
      producteur des Produits, l'Utilisateur Fournisseur étant seul responsable
      de l'obtention du ou des identifiants uniques (IDU) auprès du producteur
      et/ou du revendeur des Produits.
    </p>
    <p class="mt-5 text-sm italic">
      6.3.3 Modalités de reprise des Produits usagés
    </p>
    <p class="mt-5">
      L'Utilisateur Fournisseur assure seul la reprise des Produits soumis à
      l'obligation de reprise des produits usagés prévue à l'article L.541-10-8
      du Code de l'environnement. L'Utilisateur Fournisseur s'engage par
      conséquent à mettre en œuvre, à la demande de l'Utilisateur Adhérent, la
      reprise sans frais d'un Produit usagé dans les conditions prévues par la
      réglementation applicable.
    </p>
    <p class="mt-5">
      A ce titre, l'Utilisateur Fournisseur s'engage à faire figurer dans
      l'Offre publiée sur la Marketplace les informations relatives aux
      conditions, modalités et limites de la reprise des Produits auprès de
      l'Utilisateur Adhérent.
    </p>
    <p class="mt-5 font-bold">
      6.4 Passation d'une commande par l'Utilisateur Adhérent
    </p>
    <p class="mt-5 text-sm italic">6.4.1 Navigation sur la Marketplace</p>
    <p class="mt-5">
      L'Utilisateur Adhérent peut prendre connaissance des différentes Offres
      proposées par un Fournisseur sur la Marketplace et naviguer librement sur
      les différentes pages de la Marketplace, sans pour autant être engagé au
      titre d'une commande.
    </p>
    <p class="mt-5 text-sm italic">6.4.2 Enregistrement d'une commande</p>
    <p class="mt-5">
      Si l'Utilisateur Adhérent souhaite passer commande, il choisira les
      différentes Offres auxquelles il porte un intérêt, et manifestera ledit
      intérêt en cliquant sur la case « Acheter ». Sur la Marketplace, à tout
      moment, l'Utilisateur Adhérent pourra :
    </p>
    <ul class="ml-5 mt-3">
      <li>
        - obtenir le détail des Offres qu'il a sélectionnées, en cliquant sur «
        Voir mon panier »,
      </li>
      <li>
        - poursuivre sa sélection d'Offres en cliquant sur « Continuer mes
        achats »,
      </li>
      <li>
        - terminer sa sélection d'Offres et commander ces Offres en cliquant sur
        « Commander ».
      </li>
    </ul>
    <p class="mt-5">
      Pour commander les Offres qu'il a ainsi choisies, après avoir cliqué sur «
      Commander », l'Utilisateur Adhérent devra valider l'adresse de livraison.
      Un bon de commande apparaîtra à l'écran, détaillant notamment : les
      nature, quantité et prix des Offres retenues par l'Utilisateur Adhérent,
      ainsi que le montant total de la commande, les coordonnées de
      l'Utilisateur Adhérent, la date limite de livraison des Offres, l'adresse
      de livraison des Offres. L'Utilisateur Adhérent pourra corriger les
      éventuelles erreurs avant de confirmer sa commande. Si tel est le cas, un
      nouveau bon de commande sera automatiquement édité.
    </p>
    <p class="mt-5 text-sm italic">
      6.4.3 Validation définitive de la commande
    </p>
    <p class="mt-5">
      Après avoir pris connaissance du bon de commande, et une fois que
      l'ensemble des informations demandées aura été complété par l'Utilisateur
      Adhérent, ce dernier cochera la case d'acceptation des conditions
      générales de vente du Fournisseur et des présentes CGU et cliquera sur «
      Confirmer la commande ». La validation de la commande comporte la mention
      « Commande avec obligation de paiement ». Ce deuxième clic constitue une
      signature électronique. Cette signature a valeur et engage l'Utilisateur
      Adhérent au même titre qu'une signature manuscrite. Le bon de commande
      sera enregistré sur les registres informatiques de QANTIS, eux-mêmes
      conservés sur un support fiable et durable et sera considéré comme preuve
      de l'engagement de l'Utilisateur Adhérent. L'Utilisateur Adhérent pourra
      choisir le mode de paiement qu'il souhaite, parmi ceux proposés par QANTIS
      et procédera au paiement des Offres dans les conditions de l'article 6.5.
    </p>
    <p class="mt-5 text-sm italic">6.4.4 Récapitulatif de la commande</p>
    <p class="mt-5">
      Lorsqu'il aura validé son mode de paiement sur la Marketplace, un
      récapitulatif de la commande de l'Utilisateur Adhérent s'affichera et
      mentionnera notamment le numéro de la transaction. La commande ne sera
      considérée comme validée qu'après réception par le vendeur d'un mail de
      confirmation (un par vendeur dans le cas d'une commande « multivendeurs »)
      adressé par la Marketplace. Il sera alors envoyé à l'Utilisateur Adhérent
      un courriel récapitulant la commande lors de son enregistrement.
    </p>
    <p class="mt-5 text-sm italic">6.4.5 Suivi de la commande</p>
    <p class="mt-5">
      L'Utilisateur Adhérent peut à tout moment consulter l'état de sa commande
      en consultant la rubrique « Mes commandes ». Ce suivi permet à
      l'Utilisateur Adhérent de connaître l'état de traitement de sa commande,
      mais également l'état d'expédition ou de livraison de ses colis.
    </p>
    <p class="mt-5 font-bold">
      6.5 Paiement d'une commande par l'Utilisateur Adhérent
    </p>
    <p class="mt-5">
      Au moment de procéder au paiement de sa commande, l'Utilisateur Adhérent
      est redirigé vers la page web sécurisée du prestataire de paiement en
      ligne sélectionné par QANTIS (ci-après « Prestataire de paiement en ligne
      »).
    </p>
    <p class="mt-5">
      A ce stade, l'Utilisateur Adhérent a toujours la possibilité d'annuler sa
      commande sans frais ni pénalité.
    </p>
    <p class="mt-5">
      Le paiement de la Prestation s'effectue en totalité au moment de la
      passation de la commande, en euros et toutes taxes comprises.
    </p>
    <p class="mt-5">
      Les moyens de paiement disponibles au jour de l'entrée en vigueur des
      présentes CGU sont les suivants :
    </p>
    <ul class="ml-5 mt-3">
      <li>- paiement par carte bancaire,</li>
      <li>- paiement par virement bancaire.</li>
    </ul>
    <p class="mt-5">
      En cas de paiement par carte bancaire, l'Utilisateur Adhérent devra
      transmettre le nom du titulaire de la carte, le numéro de carte bancaire,
      suivant le type de cette dernière, la date d'expiration de celle-ci ainsi
      que le numéro cryptogramme (numéro à 3 chiffres figurant au verso de la
      carte bancaire).
    </p>
    <p class="mt-5">
      Toutes les données bancaires saisies font l'objet d'un traitement sécurisé
      et sont uniquement accessibles au Prestataire de paiement en ligne, afin
      qu'il puisse se mettre en relation avec la banque de l'Adhérent.
      L'Utilisateur Adhérent reconnaît qu'à aucun moment ses données bancaires
      ne transiteront sur le système informatique de QANTIS, qui ne pourra donc
      voir sa responsabilité engagée en cas de soustraction frauduleuse des
      données bancaires de l'Utilisateur Adhérent.
    </p>
    <p>
      Une fois le paiement effectué, un accusé réception de sa commande est
      communiqué à l'Utilisateur Adhérent et une notification est transmise par
      email au Fournisseur.
    </p>
    <p class="mt-5 font-bold">6.6 Exécution de la commande</p>
    <p class="mt-5">
      Une fois le paiement de la commande par l'Utilisateur Adhérent effectué et
      le Fournisseur notifié, ce dernier procède au traitement et à la livraison
      de la commande conformément à ses conditions générales de vente.
    </p>
    <p class="mt-5">
      A défaut d'exécution de la commande par le Fournisseur dans les délais,
      l'Utilisateur Adhérent doit en informer QANTIS afin que cette dernière
      intervienne auprès du Prestataire de paiement en ligne, le cas échéant,
      conformément à l'article 6.6 ci-après.
    </p>
    <p class="mt-5 font-bold">
      6.7 Mise sous séquestre et paiement du montant de la commande au
      Fournisseur
    </p>
    <p class="mt-5">
      Lorsque l'Utilisateur Adhérent procède au paiement de sa commande en ligne
      via le Prestataire de paiement en ligne, les sommes correspondantes sont
      immédiatement séquestrées par ce dernier.
    </p>
    <p class="mt-5">
      Le déblocage des fonds et le paiement du montant de la commande au
      Fournisseur est effectué par le Prestataire de paiement en ligne de
      manière automatique à l'issue d'un délai de 14 jours correspondant au
      délai de livraison, suivi d'un délai de 7 jours correspondant au délai de
      rétractation, à compter du paiement de la commande, sauf intervention de
      la part de QANTIS auprès du Prestataire de paiement en ligne pour bloquer
      le paiement.
    </p>
    <p class="mt-5">
      Le cas échéant, les frais de fonctionnement facturés par QANTIS
      conformément à l'article 7 ci-après sont déduits du montant versé par le
      Prestataire de paiement en ligne au Fournisseur.
    </p>
    <p class="mt-5">
      Pour bénéficier du paiement du montant de la commande, l'Utilisateur
      Fournisseur est informé qu'il devra se créer un compte auprès du
      Prestataire de paiement en ligne et accepter les Conditions Générales
      d'Utilisation qui lui seront communiquées par ce dernier.
    </p>
    <p class="mt-5 font-bold">6.8 Facturation</p>
    <p class="mt-5">
      Une facture sera émise par la Marketplace (et disponible sur l'espace
      dédiée à l'adhérent comme sur celui dédié au vendeur) pour le compte du
      vendeur.
    </p>
    <p class="mt-5">
      L'Adhérent pourra retrouver ses factures par l'intermédiaire de son
      compte.
    </p>
    <p class="mt-5">
      Le cas échéant, lorsque les frais de fonctionnement facturés par QANTIS
      conformément à l'article 7 ci-après sont déduits du montant versé par le
      Prestataire de paiement en ligne au Fournisseur, une facture est émise par
      la Marketplace au nom de QANTIS et à destination du Fournisseur.
    </p>
    <p class="mt-5 font-bold">
      7 - <span class="underline">Frais de fonctionnement</span>
    </p>
    <p class="mt-5">
      En contrepartie des Services qu'elle propose aux Utilisateurs via la
      Marketplace, QANTIS facturera au Fournisseur des frais de gestion et des
      frais techniques définis dans le contrat-cadre de référencement.
    </p>
    <p class="mt-5">
      Ces frais sont intégralement supportés par le Fournisseur à l'exclusion de
      l'Adhérent, ce que le Fournisseur reconnaît et accepte.
    </p>
    <p class="mt-5 font-bold">
      8 - <span class="underline">Obligations de l'Utilisateur</span>
    </p>
    <p class="mt-5 font-bold">
      8.1 Obligations relatives à l'utilisation de la Marketplace
    </p>
    <p class="mt-5">
      Dans le cadre de l'utilisation de la Marketplace, l'Utilisateur s'engage à
      :
    </p>
    <ul class="ml-5 mt-3">
      <li>
        - déclarer des informations complètes et exactes lors de son
        inscription, et à les mettre à jour en cas de changement,
      </li>
      <li>
        - ne pas utiliser la Marketplace à des fins frauduleuses ou non prévues
        par les présentes CGU,
      </li>
      <li>
        - ne pas usurper d'identité ni tenter de se connecter à un compte autre
        que le sien,
      </li>
      <li>
        - ne pas se livrer à des analyses du système assimilables à du reverse
        engineering,
      </li>
      <li>
        - ne pas télécharger de virus ou de fichiers potentiellement dangereux
        pour l'intégrité de la Marketplace.
      </li>
    </ul>
    <p class="mt-5 font-bold">
      8.2 Obligations relatives au contenu diffusé par l'Utilisateur sur la
      Marketplace
    </p>
    <p class="mt-5">
      L'Utilisateur est seul responsable du contenu qu'il publie sur la
      Marketplace. En particulier, l'Utilisateur Fournisseur est seul
      responsable du contenu qu'il publie et administre sur sa Page Fournisseur.
    </p>
    <p class="mt-5">
      L'Utilisateur garantit que son contenu respecte l'ordre public et les
      bonnes mœurs, ne porte pas atteinte aux droits de tiers, notamment au
      regard de la propriété intellectuelle, à la vie privée, à la réputation et
      à l'image de tiers, ne contient aucun propos dénigrant, diffamatoire ou
      discriminant à l'égard d'un autre Utilisateur, et qu'il est conforme à la
      réglementation applicable en la matière.
    </p>
    <p class="mt-5">
      L'Utilisateur s'interdit également de publier sur la Marketplace du
      contenu à des fins publicitaires et/ou promotionnelles, ni de liens
      hypertextes renvoyant vers des pages Internet sans relation avec l'objet
      de la Marketplace.
    </p>
    <p class="mt-5">
      <span class="font-bold">8.3</span> Tout manquement par l'Utilisateur à
      l'une des obligations définies aux articles 8.1 et 8.2 sera susceptible
      d'entraîner la clôture de son compte par QANTIS.
    </p>
    <p class="mt-5">
      Si QANTIS doit clôturer le compte d'un Utilisateur pour non-respect de ses
      obligations au titre des présentes CGU, elle sera en droit de résilier la
      Licence conformément à l'article 14.2, sans préjudice de tous
      dommages-intérêts qu'elle pourrait être en droit de demander, ainsi que
      d'éventuelles poursuites pénales lorsque le comportement reproché est
      susceptible d'être qualifié d'infraction pénale.
    </p>
    <p class="mt-5 font-bold">
      9 - <span class="underline">Propriété intellectuelle</span>
    </p>
    <p class="mt-5">
      <span class="font-bold">9.1</span> L'ensemble des droits de propriété
      intellectuelle relatifs à la Marketplace ainsi que tous les éléments qui
      la composent (logiciel, droits d'auteurs, marque « QANTIS », logos…) sont
      la propriété pleine et entière de QANTIS et/ou ses affiliées.
    </p>
    <p class="mt-5">
      Les droits de propriété intellectuelle éventuellement attachés aux
      Produits et Prestations mis en ligne sur la Marketplace par le Fournisseur
      appartiennent à ce dernier, lequel est éditeur des Offres qu'il rédige et
      met en ligne dans le cadre de l'utilisation de la Marketplace.
    </p>
    <p class="mt-5">
      L'Utilisateur s'engage à ne pas porter atteinte aux droits de propriété
      intellectuelle de QANTIS. L'Utilisateur s'engage à ne pas mettre à la
      disposition de tiers non autorisés, la documentation associée à la
      Marketplace, directement ou indirectement, à quelque titre, sous quelque
      forme et pour quelque cause que ce soit.
    </p>
    <p class="mt-5">
      <span class="font-bold">9.2</span> Moyennant l'acceptation des présentes
      CGU, QANTIS concède à l'Utilisateur, pour ses besoins propres, un droit
      personnel, non-cessible et non-exclusif d'utilisation de la Marketplace
      pendant toute la durée du Contrat, et ce exclusivement sur ordinateur,
      smartphone ou tablette numérique.
    </p>
    <p class="mt-5">Il est notamment interdit à l'Utilisateur:</p>
    <ul class="ml-5 mt-3">
      <li>
        - de réaliser des copies de la Marketplace ou d'un élément quelconque de
        la Marketplace,
      </li>
      <li>
        - de distribuer à des tiers des copies de la Marketplace ou de son
        contenu,
      </li>
      <li>
        - d'utiliser les spécifications de la Marketplace pour créer ou
        permettre la création d'un programme ayant la même destination,
      </li>
      <li>
        - de modifier, d'altérer, de réviser ou de décompiler la Marketplace à
        quelque fin que ce soit,
      </li>
      <li>
        - d'utiliser ses mots de passe et/ou identifiant à des fins autres que
        leur stricte finalité d'authentification,
      </li>
      <li>
        - de céder, louer, sous-louer ou transférer sa Licence d'utilisation à
        un tiers sans le consentement préalable écrit de QANTIS,
      </li>
      <li>- de ne pas respecter les fonctionnalités de la Marketplace.</li>
    </ul>
    <p class="mt-5">
      L'Utilisateur s'engage à ne pas porter atteinte aux droits de propriété
      intellectuelle de QANTIS. L'Utilisateur s'engage à ne pas mettre à la
      disposition de tiers non autorisés, la documentation associée à la
      Marketplace, directement ou indirectement, à quelque titre, sous quelque
      forme et pour quelque cause que ce soit.
    </p>
    <p class="mt-5">
      <span class="font-bold">9.2</span> Moyennant l'acceptation des présentes
      CGU, QANTIS concède à l'Utilisateur, pour ses besoins propres, un droit
      personnel, non-cessible et non-exclusif d'utilisation de la Marketplace
      pendant toute la durée du Contrat, et ce exclusivement sur ordinateur,
      smartphone ou tablette numérique.
    </p>
    <p class="mt-5">
      Cette Licence d'utilisation est exclusive de la concession de tout autre
      droit et n'emporte en aucun cas le droit de réaliser tout acte non
      expressément autorisé, notamment les droits de traduction, d'adaptation,
      d'arrangement ou toute autre modification de la Marketplace et/ou de ses
      composants, y compris dans le cadre de sa destination contractuelle.
    </p>
    <p class="mt-5">
      QANTIS est autorisée à effectuer, à tout moment et sans préavis, un
      contrôle de l'utilisation faite par l'Utilisateur de la Marketplace, afin
      de vérifier le respect de la Licence qui lui est octroyée.
    </p>
    <p class="mt-5 font-bold">
      10 - <span class="underline">Confidentialité</span>
    </p>
    <p class="mt-5">
      Les Parties s'engagent à garder secrètes les informations de toute nature
      qu'elles auraient pu recueillir à l'occasion de l'utilisation et/ou de
      l'exploitation de la Marketplace. QANTIS s'engage notamment à maintenir la
      confidentialité des informations recueillies lors de l'utilisation de la
      Marketplace par l'Utilisateur et relatives à celle-ci.
    </p>
    <p class="mt-5">
      A la résiliation de l'inscription de l'Utilisateur pour quelque cause que
      ce soit, cette obligation restera en vigueur pour une période de cinq (5)
      ans.
    </p>
    <p class="mt-5">
      Cette obligation ne s'applique pas aux informations qui :
    </p>
    <ul class="ml-5 mt-3">
      <li>
        - sont déjà en possession de la Partie à laquelle elles ont été
        communiquées, ou qui les a obtenues avant l'inscription de l'Utilisateur
        sur la Marketplace,
      </li>
      <li>- font partie du domaine public,</li>
      <li>
        - auront été expressément désignées comme non-confidentielles par la
        Partie émettrice,
      </li>
      <li>
        - ont été obtenues par l'une des Parties auprès d'un tiers non soumis à
        un accord de confidentialité avec la Partie détenant initialement les
        informations et sans relation avec l'exploitation et/ou l'utilisation de
        la Marketplace,
      </li>
      <li>
        - ont été obtenues ou développées par l'une des Parties sans l'aide des
        informations communiquées par l'autre Partie,
      </li>
      <li>- doivent légalement être divulguées.</li>
    </ul>
    <p class="mt-5 font-bold">
      11 - <span class="underline">Données personnelles</span>
    </p>
    <p class="mt-5">
      Au moment de l'inscription et de toute mise à jour des informations
      personnelles de l'Utilisateur sur la Marketplace, QANTIS recueille des
      données personnelles de l'Utilisateur au sens de la loi Informatique et
      Libertés du 6 janvier 1978 modifiée et du Règlement Général Européen sur
      la protection des Données Personnelles et notamment les données
      mentionnées à l'article 4, aux fins de traitement de sa demande
      d'inscription et de mise à disposition des Services sur la Marketplace.
    </p>
    <p class="mt-5">
      Pour s'informer en détails sur la manière dont QANTIS opère les
      traitements de données personnelles et exercer ses droits, l'Utilisateur
      est invité à consulter la politique de confidentialité de QANTIS
      disponible à l'adresse suivante :
      <a
        href="https://achat.qantis.co/politique-de-confidentialite"
        target="_blank"
        >https://achat.qantis.co/politique-de-confidentialite</a
      >.
    </p>
    <p class="mt-5">
      Ne partagez jamais vos identifiant et mot de passe. Si toutefois vous
      décidiez malgré tout ponctuellement de le faire, changez ensuite
      systématiquement votre mot de passe.
    </p>
    <p class="mt-5 font-bold">
      12 - <span class="underline">Garantie - Responsabilité</span>
    </p>
    <p class="mt-5">
      <span class="font-bold">12.1</span> Chacun des Utilisateurs est seul
      responsable de ses actes, de ses tarifs et de ses délais une fois la mise
      en relation effectuée par QANTIS.
    </p>
    <p class="mt-5">
      En tant qu'opérateur de la Marketplace, QANTIS ne fournit aucune assurance
      ou garantie relativement aux Produits et Prestations commercialisées par
      le Fournisseur.
    </p>
    <p class="mt-5">
      En conséquence, en aucun cas QANTIS ne saurait être considérée responsable
      en cas de défaillance du Fournisseur dans l'exécution du contrat conclu
      avec l'Adhérent suite à leur mise en relation via la Marketplace et plus
      généralement pour tout manquement intervenant entre le Fournisseur et
      l'Adhérent, en dehors d'une faute de QANTIS.
    </p>
    <p class="mt-5">
      En cas de défaillance du Fournisseur dans l'exécution de tout ou partie du
      contrat conclu avec l'Adhérent, ce dernier devra s'adresser directement au
      Fournisseur défaillant et initier à son encontre les réclamations et
      actions de son choix en vue de faire exécuter le contrat concerné.
    </p>
    <p class="mt-5">
      <span class="font-bold">12.2</span> QANTIS ne saurait en aucun cas être
      tenue responsable du contenu publié par l'Utilisateur sur la Marketplace,
      qu'elle ne fait qu'héberger, seul l'Utilisateur ayant la qualité d'éditeur
      de ce contenu. QANTIS pourra rechercher la responsabilité de l'Utilisateur
      pour tout préjudice qu'elle pourrait subir du fait de son contenu.
      L'Utilisateur garantit QANTIS contre toutes les conséquences dommageables
      directes et indirectes, matérielles et immatérielles pouvant découler de
      son contenu.
    </p>
    <p class="mt-5">
      <span class="font-bold">12.3</span> QANTIS assure la disponibilité et la
      continuité de la Marketplace, étant entendu qu'elle ne saurait en aucun
      cas être tenue responsable de toute interruption de la Marketplace pour
      cause de maintenance, de problèmes techniques, d'encombrement du réseau
      Internet ou de défaillance de tout autre système de télétransmission, qui
      pourraient empêcher le fonctionnement de la Marketplace.
    </p>
    <p class="mt-5">
      <span class="font-bold">12.4</span> QANTIS ne garantit pas la continuité
      et la qualité des liaisons de communications avec l'Utilisateur. Ainsi,
      les communications avec le personnel de QANTIS peuvent être interrompues
      sans préavis pour une durée raisonnable, notamment pour des raisons de
      maintenance ou pour tout fait autre qu'une faute de QANTIS.
    </p>
    <p class="mt-5">
      <span class="font-bold">12.5</span> QANTIS ne saurait être tenue
      responsable en cas d'une mauvaise exécution des Services causée par des
      informations mal renseignées ou qui n'auraient pas été mises à jour par
      l'Utilisateur.
    </p>
    <p class="mt-5">
      <span class="font-bold">12.6</span> En cas de mise en jeu de la
      responsabilité de QANTIS, en raison notamment d'un mauvais fonctionnement
      de la Marketplace, QANTIS pourra faire échec à une telle action, à sa
      seule discrétion, en corrigeant l'anomalie ou en mettant à jour la
      Marketplace.
    </p>
    <p class="mt-5">
      EN TOUTES HYPOTHESES, DANS SES RELATIONS AVEC LES UTILISATEURS, SOUS
      RESERVE DU CAS D'UNE FAUTE LOURDE OU DOLOSIVE, EN AUCUN CAS LA
      RESPONSABILITE DE QANTIS NE POURRA EXCEDER LE MONTANT PAYE PAR L'ADHERENT
      OU LE FOURNISSEUR EN CONTREPARTIE DE L'UTILISATION DE LA MARKETPLACE AU
      COURS DE L'ANNEE DURANT LAQUELLE LE PREJUDICE A ETE OCCASIONNE.
    </p>
    <p class="mt-5">
      QANTIS ne saurait en aucun cas être tenue responsable de tout dommage
      immatériel et/ou indirect, notamment perte d'exploitation, perte de
      clientèle, préjudice commercial, atteinte à l'image de marque, résultant
      de l'utilisation de la Marketplace.
    </p>
    <p class="mt-5">
      <span class="font-bold">12.7</span> Les Parties déclarent expressément
      qu'elles sont et demeureront, pendant toute la durée de leurs relations,
      des partenaires commerciaux et professionnels indépendants à tous égards.
    </p>
    <p class="mt-5 font-bold">
      13 - <span class="underline">Maintenance</span>
    </p>
    <p class="mt-5">
      En cas de maintenance programmée, le fonctionnement de la Marketplace peut
      être suspendu moyennant le respect d'un préavis de sept (7) jours.
    </p>
    <p class="mt-5">
      Par ailleurs, tout cas de force majeure tel qu'habituellement reconnu par
      les tribunaux peut entrainer la suspension de la Marketplace sans préavis
      pour cause de maintenance.
    </p>
    <p class="mt-5 font-bold">
      14 - <span class="underline">Durée - Résiliation</span>
    </p>
    <p class="mt-5">
      <span class="font-bold">14.1</span> L'inscription sur la Marketplace est
      effective à compter de la première connexion de l'Utilisateur et reste en
      vigueur, sauf résiliation conformément aux dispositions ci-après, pendant
      la durée d'octroi de la Licence conformément au contrat principal conclu
      par l'Adhérent ou le Fournisseur avec QANTIS.
    </p>
    <p class="mt-5">
      L'Utilisateur peut toutefois résilier le présent Contrat de manière
      anticipée à tout moment en formulant la demande à QANTIS sur la
      Marketplace ou par email.
    </p>
    <p class="mt-5">
      <span class="font-bold">14.2</span> Par exception à ce qui précède,
      chacune des Parties pourra résilier de plein droit le présent Contrat en
      cas de manquement de l'autre Partie à l'une de ses obligations, après une
      mise en demeure de régulariser la situation restée infructueuse pendant 10
      jours et faisant mention de l'intention de la partie lésée de son
      intention d'user du bénéfice de la présente clause. Cette résiliation
      interviendra avec effet immédiat si le manquement n'est pas régularisable.
      En cas de résiliation imputable à l'Utilisateur, ce dernier sera informé
      de la résiliation de son inscription par email.
    </p>
    <p class="mt-5">
      Seront notamment considérés comme des engagements dont l'inexécution
      pourra entraîner la rupture du Contrat, les obligations relatives au
      fonctionnement et à l'utilisation de la Marketplace, les obligations de
      paiement, l'obligation de respect des droits de propriété intellectuelle
      et l'obligation de confidentialité.
    </p>
    <p class="mt-5">
      <span class="font-bold">14.3</span> En cas de rupture du contrat-cadre de
      référencement conclu par le Fournisseur ou de la convention d'adhésion
      conclue par l'Adhérent avec QANTIS, pour quelque cause que ce soit, le
      présent Contrat conclu avec l'Utilisateur sera résilié de plein droit,
      dans les conditions (notamment de préavis) fixées auxdits contrats.
    </p>
    <p class="mt-5">
      <span class="font-bold">14.4</span> En cas de résiliation du présent
      Contrat, pour quelque cause que ce soit, l'Utilisateur ne peut plus
      bénéficier des Services.
    </p>
    <p class="mt-5 font-bold">
      15 -
      <span class="underline">Loi applicable et juridiction compétente</span>
    </p>
    <p class="mt-5">
      <span class="font-bold">15.1</span> Les présentes conditions générales, et
      plus généralement les relations entre QANTIS et l'Utilisateur, sont régies
      par le droit français.
    </p>
    <p class="mt-5">
      <span class="font-bold">15.2</span> Tout litige découlant des opérations
      visées aux présentes sera soumis à la compétence exclusive des
      juridictions du ressort de la Cour d'appel de Lyon.
    </p>
    <p class="mt-5 font-bold">
      16 - <span class="underline">Cession - Sous-traitance</span>
    </p>
    <p class="mt-5">
      <span class="font-bold">16.1</span> Le Contrat ne pourra en aucun cas
      faire l'objet d'une cession totale ou partielle, à titre onéreux ou
      gratuit, de la part de l'Utilisateur, sans autorisation écrite, expresse
      et préalable de QANTIS.
    </p>
    <p class="mt-5">
      QANTIS se réserve le droit de céder le présent Contrat sans formalités,
      sous réserve d'en informer préalablement l'Utilisateur.
    </p>
    <p class="mt-5">
      <span class="font-bold">16.2</span> QANTIS se réserve le droit de faire
      appel à tout sous-traitant de son choix dans le cadre de l'exécution du
      Contrat, en particulier de l'exécution de certaines prestations
      nécessaires à la mise à disposition des Services.
    </p>
    <p class="mt-5 font-bold">17 - <span class="underline">Tolérance</span></p>
    <p class="mt-5">
      Aucun fait de tolérance de la part de l'une des Parties à l'égard de
      l'autre ne pourra s'interpréter comme valant renonciation à se prévaloir
      de l'intégralité des droits qu'elle se voit octroyés par les présentes
      conditions générales.
    </p>
    <p class="mt-5 font-bold">18 - <span class="underline">Preuve</span></p>
    <p class="mt-5">
      En cas de litige, les Parties acceptent de considérer l'e-mail et les
      échanges via la Marketplace comme un écrit original valant preuve parfaite
      et renoncent à contester ce moyen de preuve, sauf à discuter son
      authenticité.
    </p>
  </div>
  ​
</template>

<script lang="ts" setup>
const props = defineProps({
  page: {
    type: Boolean,
    default: false,
  },
})
</script>

<style scoped>
.mentions-legales p {
  @apply text-justify !text-sm md:!text-base lg:!text-lg;
}
</style>
