<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\UserType;
use App\Models\Plan;
use App\Models\AgeGroup;
use App\Models\Author;
use App\Models\Book;
use App\Models\AuthorBook;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Activity;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\Video;
use App\Models\ActivityImage;
use App\Models\BookUserRead;
use App\Models\BookUserFavourite;
use App\Models\BookClick;
use App\Models\Report;
use App\Models\Page;
use App\Models\CommentModeration;
use App\Models\ActivityBookUser;
use App\Models\TaggingTagged;
use App\Models\ActivityBook;
use App\Models\SubscriptionApproval;
use App\Models\PointAction;
use App\Services\RankingService;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{

    protected $rankingService;

    public function __construct(RankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. User Types
        // User Types
        UserType::firstOrCreate(
            ['id' => UserType::ADMIN],
            [
                'user_type' => 'admin',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        UserType::firstOrCreate(
            ['id' => UserType::NORMAL_USER],
            [
                'user_type' => 'user',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );


        // 2. Plans
        Plan::firstOrCreate(
            ['id' => Plan::FREE],
            [
                'name' => 'Free',
                'access_level' => Plan::FREE,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        Plan::firstOrCreate(
            ['id' => Plan::PREMIUM],
            [
                'name' => 'Premium',
                'access_level' => Plan::PREMIUM,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // 3. Age Groups
        AgeGroup::firstOrCreate(['age_group' => '3-4'], ['created_at' => now(), 'updated_at' => now()]);
        AgeGroup::firstOrCreate(['age_group' => '5-6'], ['created_at' => now(), 'updated_at' => now()]);
        AgeGroup::firstOrCreate(['age_group' => '7-9'], ['created_at' => now(), 'updated_at' => now()]);

// 4. Authors
        $giles = Author::firstOrCreate(['first_name' => 'Giles', 'last_name' => 'Andreae'], [
            'description' => '• Giles Andreae (born 16 March 1966) is a British writer and illustrator. He is the creator of the stickman poet Purple Ronnie and the humorous artist/philosopher Edward Monkton, and is the author of Giraffes Can\'t Dance and many other books for children.',
            'nationality' => 'British',
            'author_photo_url' => 'authors/giles.png',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $julia = Author::firstOrCreate(['first_name' => 'Julia', 'last_name' => 'Donaldson'], [
            'description' => '• Julia Donaldson is an English children’s author. She has written more than 100 plays and books for children and teenagers. Donaldson was born on September 16, 1948, in England. As a child she wrote plays and choreographed dances, which she and her younger sister, Mary, performed. Donaldson studied drama and French at the University of Bristol. Afterward she worked in publishing and as a teacher. Donaldson went on to write some of the United Kingdom\'s best-selling picture books.',
            'nationality' => 'British',
            'author_photo_url' => 'authors/julia.png',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $eric = Author::firstOrCreate(['first_name' => 'Eric', 'last_name' => 'Carle'], [
            'description' => 'Eric Carle, (born June 25, 1929, Syracuse, New York, U.S. - died May 23, 2021, Northampton, Massachusetts), American writer and illustrator of children’s literature who published numerous best-selling books, among them The Very Hungry Caterpillar (1969), which by 2018 had sold some 50 million copies and had been translated into more than 60 languages.',
            'nationality' => 'American',
            'author_photo_url' => 'authors/eric.png',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $rachel = Author::firstOrCreate(['first_name' => 'Rachel', 'last_name' => 'Bright'], [
            'description' => 'Rachel Bright is a wordsmith, illustrator and professional thinker of happy thoughts. She has written several books for children, including Love Monster, The Lion Inside and The Koala Who Could – winner of the Evening Standard Oscar’s Book Prize and the Sainsbury’s Book Award. Her books have sold over 300,000 copies in the UK alone and been translated into over 30 languages. She is also the creator of award-winning stationery and homewares range, The Brightside. Rachel lives on a farm near the seaside, with her partner and their two young daughters.',
            'nationality' => 'British',
            'author_photo_url' => 'authors/rachel.png',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $marcus = Author::firstOrCreate(['first_name' => 'Marcus', 'last_name' => 'Pfister'], [
            'description' => 'Marcus Pfister (born 30 July 1960 in Bern, Switzerland) is a Swiss author and illustrator of children\'s picture books. His Rainbow Fish series of children\'s picture books, published since 1992, has been a worldwide success. The books have been translated into over 60 languages and have sold over 30 million copies. Decode Entertainment turned the picture books into a 26-episode animated television series of the same name, which has aired on the HBO Family television channel in the United States since 2000.',
            'nationality' => 'Swiss',
            'author_photo_url' => 'authors/marcus.jpg',
            'created_at' => now(),
            'updated_at' => now()
        ]);


// 5. Books
        $giraffes = Book::firstOrCreate([
            'title' => 'Giraffes Can\'t Dance',
            'description' => 'A story about a giraffe that can\'t dance, but learns to find his own rhythm.',
            'read_time' => 15,
            'access_level' => 1,
            'is_active' => true,
            'age_group_id' => 1
        ], [
            'cover_url' => 'covers/Giraffes Cant Dance.png',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $monkey = Book::firstOrCreate([
            'title' => 'Monkey Puzzle',
            'description' => 'A story about a monkey searching for his mother.',
            'read_time' => 10,
            'access_level' => 2,
            'is_active' => true,
            'age_group_id' => 2
        ], [
            'cover_url' => 'covers/Monkey Puzzle.png',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $brownBear = Book::firstOrCreate([
            'title' => 'Brown Bear, Brown Bear, What Do You See?',
            'description' => 'A classic story that teaches children about colors and animals.',
            'read_time' => 20,
            'access_level' => 1,
            'is_active' => true,
            'age_group_id' => 1
        ], [
            'cover_url' => 'covers/Brown Bear.png',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $koala = Book::firstOrCreate([
            'title' => 'The Koala Who Could',
            'description' => 'A story about a koala who learns to be brave.',
            'read_time' => 15,
            'access_level' => 2,
            'is_active' => true,
            'age_group_id' => 3
        ], [
            'cover_url' => 'covers/koala.jpg',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $pancakes = Book::firstOrCreate([
            'title' => 'Pancakes, Pancakes!',
            'description' => 'A delightful story about a boy trying to make pancakes.',
            'read_time' => 12,
            'access_level' => 1,
            'is_active' => true,
            'age_group_id' => 1
        ], [
            'cover_url' => 'covers/Pancakes Pancakes.png',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $fish = Book::firstOrCreate([
            'title' => 'The rainbow fish',
            'description' => 'A story about the beautiful rainbow fish.',
            'read_time' => 15,
            'access_level' => 1,
            'is_active' => true,
            'age_group_id' => 1
        ], [
            'cover_url' => 'covers/fish.png',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->command->info('Books seeded successfully!');

        // 6. Associate Authors with Books
        DB::table('author_book')->insert([
            ['author_id' => $giles->id, 'book_id' => $giraffes->id, 'created_at' => now(), 'updated_at' => now()],
            ['author_id' => $julia->id, 'book_id' => $monkey->id, 'created_at' => now(), 'updated_at' => now()],
            ['author_id' => $eric->id, 'book_id' => $brownBear->id, 'created_at' => now(), 'updated_at' => now()],
            ['author_id' => $rachel->id, 'book_id' => $koala->id, 'created_at' => now(), 'updated_at' => now()],
            ['author_id' => $eric->id, 'book_id' => $pancakes->id, 'created_at' => now(), 'updated_at' => now()],
            ['author_id' => $marcus->id, 'book_id' => $fish->id, 'created_at' => now(), 'updated_at' => now()],
        ]);


        // 7. Insert Users
        $adminUser = User::firstOrCreate(
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'user_type_id' => UserType::ADMIN, // 1
                'email' => 'admin@example.com',
                'password' => bcrypt('adminpassword1'),
                'status' => 'active',
                'email_verified_at' => now(),
                'birth_date' => '1990-01-01',
                'user_photo_url' => 'users/admin.png' // Caminho para a imagem do admin
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );


        $premiumUser = User::firstOrCreate(
            [
                'first_name' => 'Pedro',
                'last_name' => 'Silva',
                'user_type_id' => UserType::NORMAL_USER,  // 2
                'email' => 'pedro.silva@example.com',
                'password' => bcrypt('password123'),
                'status' => 'active',
                'email_verified_at' => now(),
                'birth_date' => '1995-05-15'
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $anaUser = User::firstOrCreate(
            [
                'first_name' => 'Ana',
                'last_name' => 'Santos',
                'user_type_id' => UserType::NORMAL_USER,  // 2
                'email' => 'ana.santos@example.com',
                'password' => bcrypt('password123'),
                'status' => 'active',
                'email_verified_at' => now(),
                'birth_date' => '1992-08-23'
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $luisUser = User::firstOrCreate(
            [
                'first_name' => 'Luís',
                'last_name' => 'Oliveira',
                'user_type_id' => UserType::NORMAL_USER,  // 2
                'email' => 'luis.oliveira@example.com',
                'password' => bcrypt('password123'),
                'status' => 'active',
                'email_verified_at' => now(),
                'birth_date' => '1998-03-10'
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Novo utilizador Maria
        $mariaUser = User::firstOrCreate(
            [
                'first_name' => 'Maria',
                'last_name' => 'Silva',
                'user_type_id' => UserType::NORMAL_USER,  // 2
                'email' => 'maria.silva@example.com',
                'password' => bcrypt('password123'),
                'status' => 'active',
                'email_verified_at' => now(),
                'birth_date' => '1985-11-25'
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Novo utilizador João
        $joaoUser = User::firstOrCreate(
            [
                'first_name' => 'João',
                'last_name' => 'Pereira',
                'user_type_id' => UserType::NORMAL_USER,  // 2
                'email' => 'joao.pereira@example.com',
                'password' => bcrypt('password123'),
                'status' => 'active',
                'email_verified_at' => now(),
                'birth_date' => '1990-04-18'
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Novo utilizador Sofia
        $sofiaUser = User::firstOrCreate(
            [
                'first_name' => 'Sofia',
                'last_name' => 'Costa',
                'user_type_id' => UserType::NORMAL_USER,  // 2
                'email' => 'sofia.costa@example.com',
                'password' => bcrypt('password123'),
                'status' => 'active',
                'email_verified_at' => now(),
                'birth_date' => '1993-07-02'
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Novo utilizador Tiago
        $tiagoUser = User::firstOrCreate(
            [
                'first_name' => 'Tiago',
                'last_name' => 'Monteiro',
                'user_type_id' => UserType::NORMAL_USER,  // 2
                'email' => 'tiago.monteiro@example.com',
                'password' => bcrypt('password123'),
                'status' => 'active',
                'email_verified_at' => now(),
                'birth_date' => '1988-09-14'
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Novo utilizador Clara
        $claraUser = User::firstOrCreate(
            [
                'first_name' => 'Clara',
                'last_name' => 'Ferreira',
                'user_type_id' => UserType::NORMAL_USER,  // 2
                'email' => 'clara.ferreira@example.com',
                'password' => bcrypt('password123'),
                'status' => 'active',
                'email_verified_at' => now(),
                'birth_date' => '1997-01-12'
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );


        // 8. Insert Subscriptions
// Inserir Subscrições e aprovações automáticas para Free
        $users = [$premiumUser, $anaUser, $luisUser, $mariaUser, $joaoUser, $sofiaUser, $tiagoUser, $claraUser];

        foreach ($users as $user) {
            // Criar subscrição Free para cada utilizador
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => Plan::FREE, // Plano definido como Free
                'status' => 'active', // Estado inicial definido como ativo
                'start_date' => now(),
                'end_date' => null, // Sem data de término
                'is_renewable' => true, // Subscrição renovável por padrão
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Criar entrada na tabela de aprovações para a subscrição Free
            SubscriptionApproval::create([
                'subscription_id' => $subscription->id,
                'user_id' => null, // Administrador que aprova automaticamente
                'status' => 'approved',
                'notes' => 'Automatically approved Free subscription during registration.',
                'plan_name' => 'Free', // Nome do plano armazenado para histórico
                'approval_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        // 9. Insert Activities
        // Para "Giraffes Can't Dance" (Tema: Be an Artist e Be a Cook) - id1
        $activity1 = Activity::firstOrCreate(
            ['title' => 'Be an Artist: Giraffe Handprint Art', 'description' => 'Use yellow ink for a handprint giraffe and brown for spots and details.'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // id2
        $activity2 = Activity::firstOrCreate(
            ['title' => 'Be a Cook: Giraffe Pancake Food Art', 'description' => 'Make giraffe-shaped pancakes using your favorite recipe and decorate with fruits and chocolate.'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // Para "Monkey Puzzle" (Tema: Be a Scientist) - id3
        $activity3 = Activity::firstOrCreate(
            ['title' => 'Be a Scientist: Animal Matching Puzzle', 'description' => 'Create a puzzle where kids match animals to their habitats, inspired by "Monkey Puzzle".'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // Para "Brown Bear, Brown Bear" (Tema: Be a Musician) - id4
        $activity4 = Activity::firstOrCreate(
            ['title' => 'Be a Musician: Color Song and Dance', 'description' => 'Sing and dance to a song where each color is associated with an animal, like in "Brown Bear, Brown Bear".'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // Para "The Koala Who Could" (Tema: Be a Builder) - id5
        $activity5 = Activity::firstOrCreate(
            ['title' => 'Be a Builder: Koala House Building', 'description' => 'Use blocks or playdough to build a treehouse for the koala from "The Koala Who Could".'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // Para "The Koala Who Could" (Tema: Be an Artist) - id6
        $activity6 = Activity::firstOrCreate(
            ['title' => 'Be an Artist: Koala Coloring Page', 'description' => 'Color the koala and his surroundings in this fun coloring activity inspired by "The Koala Who Could".'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // Para "Pancakes, Pancakes!" (Tema: Be a Cook) - id7
        $activity7 = Activity::firstOrCreate(
            [
                'title' => 'Be a Cook: Make Pancakes Together',
                'description' => 'Follow this super simple recipe to make pancakes, just like in the story "Pancakes, Pancakes!".

            Ingredients:
            - 1 cup of flour
            - 1 cup of milk
            - 1 egg

            Instructions:
            1. Mix the flour, milk, and egg in a bowl until smooth.
            2. Heat a non-stick pan over medium heat and pour a small amount of batter into the pan.
            3. Cook until bubbles form, then flip and cook the other side until golden.
            4. Serve and enjoy your pancakes with syrup or fruit!

            Happy cooking!'
            ],
            ['created_at' => now(), 'updated_at' => now()]
        );



        // 10. Insert Videos
        Video::firstOrCreate(
            ['title' => 'Giraffes Can\'t Dance - Giles Andreae', 'book_id' => $giraffes->id, 'video_url' => 'https://www.youtube.com/watch?v=Zzb5Acl-n70'],
            ['created_at' => now(), 'updated_at' => now()]
        );


        Video::firstOrCreate(
            ['title' => 'Storytime for kids read aloud - Monkey Puzzle by Julia Donaldson', 'book_id' => $monkey->id, 'video_url' => 'https://www.youtube.com/watch?v=r7JiKdKA7hY'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        Video::firstOrCreate(
            ['title' => 'Brown Bear, Brown Bear, What Do You See? Song | Kids Songs | Eric Carle Book', 'book_id' => $brownBear->id, 'video_url' => 'https://www.youtube.com/watch?v=E7tvOtt1itA'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        Video::firstOrCreate(
            ['title' => 'The Koala Who Could - Rachel Bright', 'book_id' => $koala->id, 'video_url' => 'https://www.youtube.com/watch?v=VcdVsRfUbjk'],
            ['created_at' => now(), 'updated_at' => now()]
        );


        Video::firstOrCreate(
            ['title' => 'Pancakes, Pancakes! – A read-aloud children\'s book by Eric Carle', 'book_id' => $pancakes->id, 'video_url' => 'https://www.youtube.com/watch?v=5-9ljf-XK9U'],
            ['created_at' => now(), 'updated_at' => now()]
        );


        // 11. Insert Tags
        $animalsTag = Tag::firstOrCreate(['name' => 'Animals'], ['created_at' => now(), 'updated_at' => now()]);
        $artTag = Tag::firstOrCreate(['name' => 'Art'], ['created_at' => now(), 'updated_at' => now()]);
        $sportsTag = Tag::firstOrCreate(['name' => 'Activities and Sports'], ['created_at' => now(), 'updated_at' => now()]);
        $coloursTag = Tag::firstOrCreate(['name' => 'Colours'], ['created_at' => now(), 'updated_at' => now()]);
        $clothesTag = Tag::firstOrCreate(['name' => 'Clothes'], ['created_at' => now(), 'updated_at' => now()]);
        $christmasTag = Tag::firstOrCreate(['name' => 'Christmas'], ['created_at' => now(), 'updated_at' => now()]);
        $celebrationsTag = Tag::firstOrCreate(['name' => 'Celebrations'], ['created_at' => now(), 'updated_at' => now()]);
        $dinosaursTag = Tag::firstOrCreate(['name' => 'Dinosaurs'], ['created_at' => now(), 'updated_at' => now()]);
        $emotionsTag = Tag::firstOrCreate(['name' => 'Emotions and Feelings'], ['created_at' => now(), 'updated_at' => now()]);
        $familyTag = Tag::firstOrCreate(['name' => 'Family'], ['created_at' => now(), 'updated_at' => now()]);
        $friendshipTag = Tag::firstOrCreate(['name' => 'Friendship'], ['created_at' => now(), 'updated_at' => now()]);
        $foodTag = Tag::firstOrCreate(['name' => 'Food'], ['created_at' => now(), 'updated_at' => now()]);
        $houseTag = Tag::firstOrCreate(['name' => 'House'], ['created_at' => now(), 'updated_at' => now()]);
        $kindnessTag = Tag::firstOrCreate(['name' => 'Kindness'], ['created_at' => now(), 'updated_at' => now()]);
        $positivityTag = Tag::firstOrCreate(['name' => 'Positivity'], ['created_at' => now(), 'updated_at' => now()]);
        $spaceTag = Tag::firstOrCreate(['name' => 'Space'], ['created_at' => now(), 'updated_at' => now()]);


        // 12. Insert Comments
        Comment::firstOrCreate(
            [
                'book_id' => $giraffes->id,
                'user_id' => $premiumUser->id,
                'comment_text' => 'A wonderful book!',
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        Comment::firstOrCreate(
            [
                'book_id' => $giraffes->id,
                'user_id' => $sofiaUser->id,
                'comment_text' => 'Great illustrations and story!',
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        Comment::firstOrCreate(
            [
                'book_id' => $monkey->id,
                'user_id' => $premiumUser->id,
                'comment_text' => 'A touching story about love and family.',
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        Comment::firstOrCreate(
            [
                'book_id' => $monkey->id,
                'user_id' => $anaUser->id,
                'comment_text' => 'Inspiring and heartwarming!',
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        Comment::firstOrCreate(
            [
                'book_id' => $giraffes->id,
                'user_id' => $sofiaUser->id,
                'comment_text' => 'Fun to read with kids!',
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // 13. Insert Activity Images
// Para a atividade de "Giraffe Handprint Art"
        ActivityImage::firstOrCreate(
            ['activity_id' => $activity1->id, 'title' => 'Giraffe Handprint Art Image', 'image_url' => 'activity_images/activities/hand_giraffe.jpg'],
            ['created_at' => now()]
        );

// Para a atividade de "Giraffe Pancake Food Art"
        ActivityImage::firstOrCreate(
            ['activity_id' => $activity2->id, 'title' => 'Giraffe Pancake Food Art Image', 'image_url' => 'activity_images/activities/giraffe-pancakes.png'],
            ['created_at' => now()]
        );

// Para a atividade de "Be a Scientist: Animal Matching Puzzle (Monkey Puzzle)"
        ActivityImage::firstOrCreate(
            ['activity_id' => $activity3->id, 'title' => 'Animal Matching Puzzle Image', 'image_url' => 'activity_images/activities/monkey_puzzle.jpg'],
            ['created_at' => now()]
        );

// Para a atividade de "Be a Musician: Color Song and Dance (Brown Bear, Brown Bear)"
        ActivityImage::firstOrCreate(
            ['activity_id' => $activity4->id, 'title' => 'Color Song and Dance Image', 'image_url' => 'activity_images/activities/brown_bear_colors.jpg'],
            ['created_at' => now()]
        );

// Para a atividade de "Be a Builder: Koala House Building (The Koala Who Could)"
        ActivityImage::firstOrCreate(
            ['activity_id' => $activity5->id, 'title' => 'Koala House Building Image', 'image_url' => 'activity_images/activities/koala_house.jpg'],
            ['created_at' => now()]
        );

// Para a atividade de "Koala Coloring Page"
        ActivityImage::firstOrCreate(
            ['activity_id' => $activity6->id, 'title' => 'Koala Coloring Page Image', 'image_url' => 'activity_images/activities/koala-coloring.jpg'],
            ['created_at' => now()]
        );

// Para a atividade de "Be a Cook: Make Pancakes Together (Pancakes, Pancakes!)"
        ActivityImage::firstOrCreate(
            ['activity_id' => $activity7->id, 'title' => 'Kids Making Pancakes Image', 'image_url' => 'activity_images/activities/pancakes.jpg'],
            ['created_at' => now()]
        );




        // 14. Insert Book User Read
        DB::table('book_user_read')->insert([
            [
                'book_id' => $giraffes->id,
                'user_id' => $premiumUser->id,
                'progress' => 50,
                'rating' => 5,
                'read_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'book_id' => $brownBear->id,
                'user_id' => $premiumUser->id,
                'progress' => 80,
                'rating' => 4,
                'read_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'book_id' => $koala->id,
                'user_id' => $premiumUser->id,
                'progress' => 100,
                'rating' => 5,
                'read_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'book_id' => $giraffes->id,
                'user_id' => $anaUser->id,
                'progress' => 100,
                'rating' => 4,
                'read_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'book_id' => $monkey->id,
                'user_id' => $anaUser->id,
                'progress' => 90,
                'rating' => 5,
                'read_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'book_id' => $brownBear->id,
                'user_id' => $luisUser->id,
                'progress' => 75,
                'rating' => 4,
                'read_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        // 15. Insert Book User Favourite
        DB::table('book_user_favourite')->insert([
            [
                'book_id' => $giraffes->id,
                'user_id' => $premiumUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'book_id' => $monkey->id,
                'user_id' => $premiumUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'book_id' => $koala->id,
                'user_id' => $anaUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'book_id' => $brownBear->id,
                'user_id' => $luisUser->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);



        // Obtém a data atual para calcular os últimos três meses
        $currentDate = Carbon::now();
        $threeMonthsAgo = $currentDate->copy()->subMonths(3);

        // 16. Insert Book Clicks
        // Para o livro "Giraffes Can't Dance" (4 cliques)
        BookClick::create([
            'book_id' => $giraffes->id,
            'clicked_at' => $threeMonthsAgo->copy()->addDays(5),
            'created_at' => $threeMonthsAgo->copy()->addDays(5),
            'updated_at' => $threeMonthsAgo->copy()->addDays(5),
        ]);
        BookClick::create([
            'book_id' => $giraffes->id,
            'clicked_at' => $threeMonthsAgo->copy()->addDays(20),
            'created_at' => $threeMonthsAgo->copy()->addDays(20),
            'updated_at' => $threeMonthsAgo->copy()->addDays(20),
        ]);
        BookClick::create([
            'book_id' => $giraffes->id,
            'clicked_at' => $currentDate->copy()->subDays(10),
            'created_at' => $currentDate->copy()->subDays(10),
            'updated_at' => $currentDate->copy()->subDays(10),
        ]);
        BookClick::create([
            'book_id' => $giraffes->id,
            'clicked_at' => $currentDate->copy()->subDays(2),
            'created_at' => $currentDate->copy()->subDays(2),
            'updated_at' => $currentDate->copy()->subDays(2),
        ]);

// Para o livro "Monkey Puzzle" (3 cliques)
        BookClick::create([
            'book_id' => $monkey->id,
            'clicked_at' => $threeMonthsAgo->copy()->addDays(10),
            'created_at' => $threeMonthsAgo->copy()->addDays(10),
            'updated_at' => $threeMonthsAgo->copy()->addDays(10),
        ]);
        BookClick::create([
            'book_id' => $monkey->id,
            'clicked_at' => $currentDate->copy()->subDays(15),
            'created_at' => $currentDate->copy()->subDays(15),
            'updated_at' => $currentDate->copy()->subDays(15),
        ]);
        BookClick::create([
            'book_id' => $monkey->id,
            'clicked_at' => $currentDate->copy()->subDays(5),
            'created_at' => $currentDate->copy()->subDays(5),
            'updated_at' => $currentDate->copy()->subDays(5),
        ]);

// Para o livro "Brown Bear, Brown Bear" (2 cliques)
        BookClick::create([
            'book_id' => $brownBear->id,
            'clicked_at' => $threeMonthsAgo->copy()->addDays(15),
            'created_at' => $threeMonthsAgo->copy()->addDays(15),
            'updated_at' => $threeMonthsAgo->copy()->addDays(15),
        ]);
        BookClick::create([
            'book_id' => $brownBear->id,
            'clicked_at' => $currentDate->copy()->subDays(7),
            'created_at' => $currentDate->copy()->subDays(7),
            'updated_at' => $currentDate->copy()->subDays(7),
        ]);

// Para o livro "The Koala Who Could" (3 cliques)
        BookClick::create([
            'book_id' => $koala->id,
            'clicked_at' => $threeMonthsAgo->copy()->addDays(18),
            'created_at' => $threeMonthsAgo->copy()->addDays(18),
            'updated_at' => $threeMonthsAgo->copy()->addDays(18),
        ]);
        BookClick::create([
            'book_id' => $koala->id,
            'clicked_at' => $currentDate->copy()->subDays(12),
            'created_at' => $currentDate->copy()->subDays(12),
            'updated_at' => $currentDate->copy()->subDays(12),
        ]);
        BookClick::create([
            'book_id' => $koala->id,
            'clicked_at' => $currentDate->copy()->subDays(3),
            'created_at' => $currentDate->copy()->subDays(3),
            'updated_at' => $currentDate->copy()->subDays(3),
        ]);

// Para o livro "Pancakes, Pancakes!" (1 clique)
        BookClick::create([
            'book_id' => $pancakes->id,
            'clicked_at' => $currentDate->copy()->subDays(20),
            'created_at' => $currentDate->copy()->subDays(20),
            'updated_at' => $currentDate->copy()->subDays(20),
        ]);



        // 17. Insert Reports
        Report::firstOrCreate(
            ['report_type' => 'book_clicks', 'report_data' => json_encode(['book_id' => $giraffes->id, 'clicks' => 100])],
            ['created_at' => now()]
        );

        Report::firstOrCreate(
            ['report_type' => 'book_clicks', 'report_data' => json_encode(['book_id' => $monkey->id, 'clicks' => 75])],
            ['created_at' => now()]
        );

        Report::firstOrCreate(
            ['report_type' => 'book_clicks', 'report_data' => json_encode(['book_id' => $koala->id, 'clicks' => 90])],
            ['created_at' => now()]
        );

        // 18. Insert Pages
        Page::firstOrCreate(
            ['book_id' => $giraffes->id, 'page_image_url' => 'https://example.com/giraffes_page1', 'audio_url' => null, 'page_index' => 1],
            ['created_at' => now()]
        );

        Page::firstOrCreate(
            ['book_id' => $monkey->id, 'page_image_url' => 'https://example.com/monkey_page1', 'audio_url' => null, 'page_index' => 1],
            ['created_at' => now()]
        );

        Page::firstOrCreate(
            ['book_id' => $brownBear->id, 'page_image_url' => 'https://example.com/brown_bear_page1', 'audio_url' => null, 'page_index' => 1],
            ['created_at' => now()]
        );

        // Adicionando as páginas do livro "The Koala Who Could"
        for ($i = 0; $i <= 35; $i++) {
            Page::firstOrCreate(
                [
                    'book_id' => $koala->id,
                    'page_image_url' => "pages/koala/$i.png", // Caminho relativo baseado no nome do arquivo
                    'audio_url' => null, // Não há áudio para as páginas no momento
                    'page_index' => $i, // Índice da página
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Pages for "The Koala Who Could" seeded successfully!');

        // Adicionando as páginas do livro "The rainbow fish"
        for ($i = 0; $i <= 31; $i++) {
            Page::firstOrCreate(
                [
                    'book_id' => $fish->id,
                    'page_image_url' => "pages/fish/$i.png", // Caminho relativo baseado no nome do arquivo
                    'audio_url' => null, // Não há áudio para as páginas no momento
                    'page_index' => $i, // Índice da página
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Pages for "The Rainbow fish" seeded successfully!');

        // 19. Insert Comment Moderation
        CommentModeration::firstOrCreate(
            [
                'comment_id' => 1,
                'user_id' => $adminUser->id,
                'status' => 'approved',
                'moderation_date' => now()
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        CommentModeration::firstOrCreate(
            [
                'comment_id' => 3,
                'user_id' => $adminUser->id,
                'status' => 'rejected',
                'moderation_date' => now()
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        CommentModeration::firstOrCreate(
            [
                'comment_id' => 2,
                'user_id' => null,
                'status' => 'pending',
                'moderation_date' => null
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        CommentModeration::firstOrCreate(
            [
                'comment_id' => 4,
                'user_id' => null,
                'status' => 'pending',
                'moderation_date' => null
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        CommentModeration::firstOrCreate(
            [
                'comment_id' => 5,
                'user_id' => null,
                'status' => 'pending',
                'moderation_date' => null
            ],
            [
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // 20. Insert Activity Book Relations
        // Atividades para "Giraffes Can't Dance"
        $activityBook1 = ActivityBook::firstOrCreate(
            ['activity_id' => $activity1->id, 'book_id' => $giraffes->id] // Be an Artist: Giraffe Handprint Art
        );

        $activityBook2 = ActivityBook::firstOrCreate(
            ['activity_id' => $activity2->id, 'book_id' => $giraffes->id] // Be a Cook: Giraffe Pancake Food Art
        );

        // Atividade para "Monkey Puzzle"
        $activityBook3 = ActivityBook::firstOrCreate(
            ['activity_id' => $activity3->id, 'book_id' => $monkey->id] // Be a Scientist: Animal Matching Puzzle
        );

        // Atividade para "Brown Bear, Brown Bear"
        $activityBook4 = ActivityBook::firstOrCreate(
            ['activity_id' => $activity4->id, 'book_id' => $brownBear->id] // Be a Musician: Color Song and Dance
        );

        // Atividades para "The Koala Who Could"
        $activityBook5 = ActivityBook::firstOrCreate(
            ['activity_id' => $activity5->id, 'book_id' => $koala->id] // Be a Builder: Koala House Building
        );

        $activityBook6 = ActivityBook::firstOrCreate(
            ['activity_id' => $activity6->id, 'book_id' => $koala->id] // Be an Artist: Koala Coloring Page
        );

        // Atividade para "Pancakes, Pancakes!"
        $activityBook7 = ActivityBook::firstOrCreate(
            ['activity_id' => $activity7->id, 'book_id' => $pancakes->id] // Be a Cook: Make Pancakes Together
        );



        // 21. Insert Activity Book User for Premium and Free Users
        // Para o user premium (Pedro)
        ActivityBookUser::firstOrCreate(
            ['activity_book_id' => $activityBook1->id, 'user_id' => $premiumUser->id, 'progress' => 75],
            ['created_at' => now(), 'updated_at' => now()]
        );

        ActivityBookUser::firstOrCreate(
            ['activity_book_id' => $activityBook2->id, 'user_id' => $premiumUser->id, 'progress' => 50],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // Para o user premium (Ana)
        ActivityBookUser::firstOrCreate(
            ['activity_book_id' => $activityBook3->id, 'user_id' => $anaUser->id, 'progress' => 60],
            ['created_at' => now(), 'updated_at' => now()]
        );

        ActivityBookUser::firstOrCreate(
            ['activity_book_id' => $activityBook4->id, 'user_id' => $anaUser->id, 'progress' => 40],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // Para o user gratuito (Luís)
        ActivityBookUser::firstOrCreate(
            ['activity_book_id' => $activityBook5->id, 'user_id' => $luisUser->id, 'progress' => 30],
            ['created_at' => now(), 'updated_at' => now()]
        );

        ActivityBookUser::firstOrCreate(
            ['activity_book_id' => $activityBook6->id, 'user_id' => $luisUser->id, 'progress' => 20],
            ['created_at' => now(), 'updated_at' => now()]
        );


        // 22. Insert Tagging Tagged
        // Tags for "Giraffes Can't Dance"
        TaggingTagged::firstOrCreate(
            ['book_id' => $giraffes->id, 'tag_id' => $animalsTag->id],
            ['created_at' => now()]
        );
        TaggingTagged::firstOrCreate(
            ['book_id' => $giraffes->id, 'tag_id' => $emotionsTag->id],
            ['created_at' => now()]
        );

        // Tags for "Monkey Puzzle"
        TaggingTagged::firstOrCreate(
            ['book_id' => $monkey->id, 'tag_id' => $familyTag->id],
            ['created_at' => now()]
        );
        TaggingTagged::firstOrCreate(
            ['book_id' => $monkey->id, 'tag_id' => $sportsTag->id],
            ['created_at' => now()]
        );


        // Tags for "Brown Bear, Brown Bear"
        TaggingTagged::firstOrCreate(
            ['book_id' => $brownBear->id, 'tag_id' => $animalsTag->id],
            ['created_at' => now()]
        );
        TaggingTagged::firstOrCreate(
            ['book_id' => $brownBear->id, 'tag_id' => $coloursTag->id],
            ['created_at' => now()]
        );

        // Tags for "The Koala Who Could"
        TaggingTagged::firstOrCreate(
            ['book_id' => $koala->id, 'tag_id' => $emotionsTag->id],
            ['created_at' => now()]
        );
        TaggingTagged::firstOrCreate(
            ['book_id' => $koala->id, 'tag_id' => $kindnessTag->id],
            ['created_at' => now()]
        );

        // Tags for "Pancakes, Pancakes!"
        TaggingTagged::firstOrCreate(
            ['book_id' => $pancakes->id, 'tag_id' => $foodTag->id],
            ['created_at' => now()]
        );
        TaggingTagged::firstOrCreate(
            ['book_id' => $pancakes->id, 'tag_id' => $familyTag->id],
            ['created_at' => now()]
        );

        // 23. Insert Subscription Approval
        // Inserir pedidos pendentes para aprovação (mudança de Free para Premium)
        $pendingRequests = [
            [
                'user_email' => 'ana.santos@example.com', // Ana
            ],
            [
                'user_email' => 'luis.oliveira@example.com', // Luís
            ],
            [
                'user_email' => 'maria.silva@example.com', // Maria
            ],
        ];

        foreach ($pendingRequests as $request) {
            // Encontrar o utilizador com base no email
            $user = User::where('email', $request['user_email'])->first();

            if ($user) {
                // Encontrar a subscrição associada ao utilizador
                $subscription = Subscription::where('user_id', $user->id)->first();

                if ($subscription) {
                    // Criar pedido de aprovação pendente
                    SubscriptionApproval::create([
                        'subscription_id' => $subscription->id,
                        'user_id' => null, // Pedido ainda não analisado pelo admin
                        'status' => 'pending',
                        'notes' => 'This request is awaiting review and approval by an administrator.',
                        'plan_name' => 'Premium', // Nome do plano solicitado armazenado para histórico
                        'approval_date' => null, // Não aprovado/rejeitado ainda
                        'created_at' => now()->addMinutes(5), // Criado 5 minutos após o registo
                        'updated_at' => now()->addMinutes(5),
                    ]);
                }
            }
        }

        // 24.Insert Point Actions
        $readBook = PointAction::firstOrCreate(
            ['action_name' => 'read_book'],
            [
                'points' => 50,
                'description' => 'Points for reading a book',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $writeReview = PointAction::firstOrCreate(
            ['action_name' => 'write_review'],
            [
                'points' => 30,
                'description' => 'Points for writing a review',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $commentReview = PointAction::firstOrCreate(
            ['action_name' => 'comment_review'],
            [
                'points' => 10,
                'description' => 'Points for commenting on a review',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $completeActivity = PointAction::firstOrCreate(
            ['action_name' => 'complete_activity'],
            [
                'points' => 20,
                'description' => 'Points for completing an activity',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $dailyLogin = PointAction::firstOrCreate(
            ['action_name' => 'daily_login'],
            [
                'points' => 5,
                'description' => 'Points for daily login',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $addFavorite = PointAction::firstOrCreate(
            ['action_name' => 'add_favorite'],
            [
                'points' => 5,
                'description' => 'Points for adding a book to favorites',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $progress25 = PointAction::firstOrCreate(
            ['action_name' => 'book_progress_25'],
            [
                'points' => 10,
                'description' => 'Points for reaching 25% of book progress',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $progress50 = PointAction::firstOrCreate(
            ['action_name' => 'book_progress_50'],
            [
                'points' => 15,
                'description' => 'Points for reaching 50% of book progress',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $progress75 = PointAction::firstOrCreate(
            ['action_name' => 'book_progress_75'],
            [
                'points' => 20,
                'description' => 'Points for reaching 75% of book progress',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $progress100 = PointAction::firstOrCreate(
            ['action_name' => 'book_progress_100'],
            [
                'points' => 25,
                'description' => 'Points for completing a book (100%)',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $activityProgress50 = PointAction::firstOrCreate(
            ['action_name' => 'activity_progress_50'],
            [
                'points' => 10,
                'description' => 'Points for reaching 50% in an activity',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $activityProgress100 = PointAction::firstOrCreate(
            ['action_name' => 'activity_progress_100'],
            [
                'points' => 20,
                'description' => 'Points for completing an activity (100%)',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        $watchVideo = PointAction::firstOrCreate(
            ['action_name' => 'watch_video'],
            [
                'points' => 15,
                'description' => 'Points for watching a book video',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // 25. Insert Initial User Points and Rankings
        foreach ($users as $user) {
            // Pontos por livros lidos
            $userReadBooks = BookUserRead::where('user_id', $user->id)->get();
            foreach ($userReadBooks as $readBook) {
                // Pontos baseados no progresso
                if ($readBook->progress >= 25) {
                    $this->rankingService->addPoints($user, 'book_progress_25', $readBook->book_id, 'book');
                }
                if ($readBook->progress >= 50) {
                    $this->rankingService->addPoints($user, 'book_progress_50', $readBook->book_id, 'book');
                }
                if ($readBook->progress >= 75) {
                    $this->rankingService->addPoints($user, 'book_progress_75', $readBook->book_id, 'book');
                }
                if ($readBook->progress == 100) {
                    $this->rankingService->addPoints($user, 'book_progress_100', $readBook->book_id, 'book');
                    $this->rankingService->addPoints($user, 'read_book', $readBook->book_id, 'book');
                }
            }

            // Pontos por favoritos
            $userFavorites = BookUserFavourite::where('user_id', $user->id)->get();
            foreach ($userFavorites as $favorite) {
                $this->rankingService->addPoints($user, 'add_favorite', $favorite->book_id, 'book');
            }

            // Pontos por comentários
            $userComments = Comment::where('user_id', $user->id)->get();
            foreach ($userComments as $comment) {
                $this->rankingService->addPoints($user, 'write_review', $comment->book_id, 'book');
            }

            // Pontos por atividades
            $userActivities = ActivityBookUser::where('user_id', $user->id)->get();
            foreach ($userActivities as $activity) {
                if ($activity->progress >= 50) {
                    $this->rankingService->addPoints($user, 'activity_progress_50', $activity->activity_book_id, 'activity');
                }
                if ($activity->progress == 100) {
                    $this->rankingService->addPoints($user, 'activity_progress_100', $activity->activity_book_id, 'activity');
                    $this->rankingService->addPoints($user, 'complete_activity', $activity->activity_book_id, 'activity');
                }
            }
        }
    }
}
