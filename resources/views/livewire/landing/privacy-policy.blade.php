<div class="min-h-screen bg-[#fff8f5]">

    {{-- Navigation --}}
    <nav class="bg-white border-b border-[#c1c9b6] sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="h-10 w-auto rounded-lg" />
                <span class="font-sora font-bold text-[#1e1b18]">Agro Eco BAARA</span>
            </a>
            <a href="{{ route('home') }}" class="text-sm text-[#2c6904] font-semibold hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Retour
            </a>
        </div>
    </nav>

    {{-- Contenu --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
        <div class="bg-white rounded-2xl border border-[#c1c9b6] shadow-sm p-8 sm:p-12">

            <h1 class="font-sora text-3xl font-bold text-[#1e1b18] mb-2">Politique de confidentialité</h1>
            <p class="text-sm text-[#717a69] mb-8">Dernière mise à jour : {{ now()->format('d/m/Y') }}</p>

            <div class="prose prose-sm max-w-none text-[#41493b] space-y-6" style="text-align: justify;">

                <h2 class="font-sora text-lg font-bold text-[#1e1b18] mt-8 mb-3">1. Responsable du traitement</h2>
                <p>
                    Le responsable du traitement des données collectées via la plateforme <strong>Agro Eco BAARA</strong> est
                    l'association Agro Eco BAARA, dont le siège est situé à Ouagadougou, Burkina Faso.
                </p>

                <h2 class="font-sora text-lg font-bold text-[#1e1b18] mt-8 mb-3">2. Données collectées</h2>
                <p>Nous collectons les données suivantes :</p>
                <ul class="list-disc pl-6 space-y-1">
                    <li><strong>Formulaire de contact :</strong> nom, prénom, e-mail, téléphone (optionnel), message</li>
                    <li><strong>Dossiers candidats :</strong> identité, formation, compétences, expériences, photo d'identité, documents justificatifs</li>
                    <li><strong>Entreprises partenaires :</strong> raison sociale, contacts, activités, besoins</li>
                    <li><strong>Données techniques :</strong> adresse IP, date et heure de connexion</li>
                </ul>

                <h2 class="font-sora text-lg font-bold text-[#1e1b18] mt-8 mb-3">3. Finalités du traitement</h2>
                <p>Les données sont traitées pour :</p>
                <ul class="list-disc pl-6 space-y-1">
                    <li>Faciliter la mise en relation entre jeunes et entreprises du secteur agroécologique</li>
                    <li>Assurer le suivi des parcours d'insertion professionnelle</li>
                    <li>Répondre aux demandes de contact</li>
                    <li>Améliorer nos services et notre plateforme</li>
                </ul>

                <h2 class="font-sora text-lg font-bold text-[#1e1b18] mt-8 mb-3">4. Base juridique</h2>
                <p>
                    Le traitement est fondé sur le consentement de la personne concernée (article 7 de la loi burkinabè
                    n°010-2004/AN relative à la protection des données à caractère personnel) et sur l'intérêt légitime
                    du responsable du traitement.
                </p>

                <h2 class="font-sora text-lg font-bold text-[#1e1b18] mt-8 mb-3">5. Durée de conservation</h2>
                <ul class="list-disc pl-6 space-y-1">
                    <li><strong>Messages de contact :</strong> 12 mois après traitement</li>
                    <li><strong>Dossiers candidats :</strong> durée de l'accompagnement + 3 ans</li>
                    <li><strong>Données entreprises :</strong> durée du partenariat + 3 ans</li>
                    <li><strong>Logs techniques :</strong> 6 mois</li>
                </ul>

                <h2 class="font-sora text-lg font-bold text-[#1e1b18] mt-8 mb-3">6. Destinataires des données</h2>
                <p>
                    Les données sont accessibles uniquement aux opérateurs et administrateurs autorisés de la plateforme
                    Agro Eco BAARA. Aucune donnée n'est transmise à des tiers sans le consentement préalable de la
                    personne concernée, sauf obligation légale.
                </p>

                <h2 class="font-sora text-lg font-bold text-[#1e1b18] mt-8 mb-3">7. Sécurité</h2>
                <p>
                    Nous mettons en œuvre des mesures techniques et organisationnelles pour protéger vos données :
                    chiffrement des communications (HTTPS), contrôle d'accès par rôles, journalisation des actions,
                    sauvegardes régulières.
                </p>

                <h2 class="font-sora text-lg font-bold text-[#1e1b18] mt-8 mb-3">8. Vos droits</h2>
                <p>Conformément à la réglementation applicable, vous disposez des droits suivants :</p>
                <ul class="list-disc pl-6 space-y-1">
                    <li>Droit d'accès à vos données personnelles</li>
                    <li>Droit de rectification</li>
                    <li>Droit à l'effacement (droit à l'oubli)</li>
                    <li>Droit d'opposition au traitement</li>
                    <li>Droit à la portabilité</li>
                </ul>
                <p>
                    Pour exercer ces droits, contactez-nous via le formulaire de contact de la plateforme ou à l'adresse
                    e-mail : <strong>contact@agroecobaara.bf</strong>
                </p>

                <h2 class="font-sora text-lg font-bold text-[#1e1b18] mt-8 mb-3">9. Cookies</h2>
                <p>
                    La plateforme utilise uniquement des cookies techniques nécessaires au bon fonctionnement du service
                    (session, authentification). Aucun cookie publicitaire ou de suivi n'est utilisé.
                </p>

                <h2 class="font-sora text-lg font-bold text-[#1e1b18] mt-8 mb-3">10. Modification de cette politique</h2>
                <p>
                    Cette politique peut être mise à jour à tout moment. La date de dernière modification est indiquée
                    en haut de cette page. Nous vous invitons à la consulter régulièrement.
                </p>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-white border-t border-[#c1c9b6] py-6">
        <div class="max-w-5xl mx-auto px-4 text-center text-xs text-[#717a69]">
            &copy; {{ date('Y') }} Agro Eco BAARA. Tous droits réservés.
        </div>
    </footer>
</div>
