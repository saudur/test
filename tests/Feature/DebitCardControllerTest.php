<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DebitCard;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DebitCardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Passport::actingAs($this->user);
    }

    public function testCustomerCanSeeAListOfDebitCards()
    {
        // get /debit-cards

        // $response = $this->actingAs($this->user)->get('/debit-cards');    
        // $response->assertStatus(200); 

        // $user = factory(User::class)->create();
        // $debitCard = factory(DebitCard::class)->create(['user_id' => $user->id]);
        // $transaction = factory(DebitCardTransaction::class)->create(['debit_card_id' => $debitCard->id]);

        $response = $this->get('/debit-cards')
        ->assertJsonStructure([
            'status',
            'result' => [
                'number',
                'type',
                'expiration_date',
            ],
        ]);
    $response->assertStatus(200);
    }

    public function testCustomerCannotSeeAListOfDebitCardsOfOtherCustomers()
    {
        // get /debit-cards

        $response = $this->get('/debit-cards')
        ->assertFalse();

        $debitCards = (array)json_decode($this->response->content());
        $response->assertContains($this->user->id, $debitCards['user_id']);

    }

    public function testCustomerCanCreateADebitCard()
    {
        // post /debit-cards
        $this->post('/debit-cards');
    }

    public function testCustomerCanSeeASingleDebitCardDetails()
    {
        // get api/debit-cards/{debitCard}
        $this->post('/debit-cards', [
            'user_id' => $this->user->id,
            'type' => 'type 1',
            'number' => rand(1000000000000000, 9999999999999999),
            'expiration_date' => Carbon::now()->addYear(),
        ]);

        $newDebitCard = (array)json_decode($this->response->content());

        $this->assertArrayHasKey('user_id', $newDebitCard);
        $this->assertCount(
            1,
            $this->user->debitCards->latest()->count(), "1 data has been added"
        );
        $this->assertEquals($newDebitCard['number'], $this->user->debitCards->get());
    }

    public function testCustomerCannotSeeASingleDebitCardDetails()
    {
        // get api/debit-cards/{debitCard}
        $debitCardId = 1;
        $response = $this->get('/debit-cards', [
            'id' => $debitCardId,
        ]);

        $debitCards = (array)json_decode($this->response->content());
        $response->assertNull( 
            $debitCards, 
            "Data not available"
        ); 
    }

    public function testCustomerCanActivateADebitCard()
    {
        // put api/debit-cards/{debitCard}
        
        
    }

    public function testCustomerCanDeactivateADebitCard()
    {
        // put api/debit-cards/{debitCard}
    }

    public function testCustomerCannotUpdateADebitCardWithWrongValidation()
    {
        // put api/debit-cards/{debitCard}
    }

    public function testCustomerCanDeleteADebitCard()
    {
        // delete api/debit-cards/{debitCard}
    }

    public function testCustomerCannotDeleteADebitCardWithTransaction()
    {
        // delete api/debit-cards/{debitCard}
    }

    // Extra bonus for extra tests :)
}
