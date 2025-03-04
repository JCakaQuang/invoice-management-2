# Invoice manager
<h1>About me</h1>
<ul>
    <li>Nguyễn Văn Quang - 23011955</li></a>
</ul>
<h1>About my project</h1>
<p>This is an invoice management web application that provides users with a flexible and convenient platform to create, store, and manage invoices efficiently.
Users can register an account, create a personal profile, and track their generated invoices. The system supports adding products to invoices, automatically calculating the total amount, and updating stock quantities. Additionally, the application allows users to search, edit, and manage invoices easily, helping businesses monitor their financial transactions accurately.</p>
<p>Detailed project documentation is <a href='https://1drv.ms/w/c/edad28523a6cb810/EZHMYFVS-gVCqK0VMMb59c8B2k1mPaZ3kJqhQUQ5WXe8Xw?e=Qn2UU3'>here</a></p>

<h2>Main functions include:</h2>
<ul>
    <li>account management</li>
     <li>Manage personal page</li>
     <li>manage products</li>
     <li>Manage invoices</li>
</ul>

<h2>Use Case</h2>
<div align='center'>
    <img src='https://raw.githubusercontent.com/JCakaQuang/invoice-management/main/images/use%20case.JPG' alt='Use Case' width='600'>
</div>



<h1 align='center'>How to deploy - Local Development</h1>

Install larvel:
    
    composer create-project --prefer-dist laravel/laravel {name}
    php artisan serve

Clone the Repository:

    git clone https://github.com/miin000/myApp.git
    cd myMusicApp
    
Install Dependencies:

    composer install
    npm install
    
If not installed nodejs:

    curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash - && sudo apt-get install -y nodejs

Install Dependencies orthers:

    npm run build
    npm run dev
    
Set Up Environment Variables:

    cp .env.example .env
    php artisan key:generate
    
Configure Database Settings:

Edit the .env file to match your local database settings.

Run Migrations and Seed the Database:


    php artisan migrate --seed
    
Start the Local Development Server:

    php artisan serve --host=0.0.0.0
    
Visit the Application:

<h1 align='center'>Some picture about our website</h1>
<h2>Login, register</h2>
<div align='center'>
    <img src='https://github.com/JCakaQuang/invoice-management/blob/main/images/rregister.png'>
</div>

<h2>Home page</h2>
<div align='center' >
    <img src='https://github.com/JCakaQuang/invoice-management/blob/main/images/create_invoice.png'>
</div>
<hr>
<h2>invoice list</h2>
<div align='center' >
    <img src='https://github.com/JCakaQuang/invoice-management/blob/main/images/Invoice_list.png'>

</div>

