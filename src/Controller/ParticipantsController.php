<?php
declare(strict_types=1);

namespace App\Controller;

use App\Notification\iscrizioneOkNotification;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\I18n\FrozenTime;
use Exception;

/**
 * Participants Controller
 *
 * @property \App\Model\Table\ParticipantsTable $Participants
 *
 * @method \App\Model\Entity\Participant[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ParticipantsController extends AppController
{

	public $paginate = [
	'contain' => ['Events'],
	];

	/**
	 * Add method
	 *
	 * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
	 */
	public function add() {
		$participant = $this->Participants->newEmptyEntity();
		if ($this->request->is('post')) {
			if (isset($this->request->getData()['referal'])) {
				$referal = $this->request->getData()['referal'];
			} else {
				$referal = $this->referer();
			}

			$participant = $this->Participants->patchEntity($participant, $this->request->getData());

		  //Se siete troppi ti rimando indietro
			if ($this->Participants->checkMaxPax($participant)) {
				$message = __("L'evento è al completo ti chiediamo di scegliere un altro evento.");
				$responseData = ['message' => $message, 'success' => false];
			} else {
			  //Se il partecipante ci sta ancora, provo a salvare.
				if ($this->Participants->save($participant)) {
					$message = __("Abbiamo ricevuto correttamente la tua richiesta. Controlla la posta!");
					$responseData = ['message' => $message, 'success' => true];

				  //Mi servono i dati dell'evento
					try {
						$event = $this->Participants->Events->get($participant->event_id);
					} catch (Exception $e) {
						$this->Flash->error("impossibile recuperare i dati dell'evento per mandare una mail al destinatario");

						return;
					}

				  //Mando una mail di notifica
					$n = new iscrizioneOkNotification($participant, $event);
					$n->toMail();
				} else {
					$message = __("Ci dev'essere qualche errore, per favore controlla i messaggi e riprova a salvare.");
					$responseData = ['message' => $message, 'success' => false];
				}
			}

		//Mando la risposta
			if ($this->request->isAjax()) {
			  // Specify which view vars JsonView should serialize.
				$this->set('responseData', $responseData);
				$this->set('_serialize', 'responseData');
				$this->response->getStatusCode(200);
				$this->RequestHandler->renderAs($this, 'json');
			} else {
				if ($responseData['success']) {
					$this->Flash->success($message);
				} else {
					$this->Flash->error($message);
				}
		  //Se fai click su payment ti mando al pulsante del pagamento
				if ($this->request->getData('payment')) {
					return $this->redirect(['action' => 'payment', $participant->id]);
				}

				return $this->redirect($referal);
			}
		}
		$events = $this->Participants->Events->find('list', ['limit' => 200]);
		$this->set(compact('participant', 'events'));
	}

	public function payment($pid): void {
		$p = $this->Participants->get($pid, [
		'contain' => ['Events'],
		]);
		$this->set('name', $p->name);
		$this->set('title', $p->event->title);
		$this->set('amount', $p->event->cost);
		$this->set('pid', $pid);
	}

	public function thankyou($pid, $transaction_id): void {
	  //TODO: devo controllare se il pagamento è davvero andato a buon fine lato server
		$p = $this->Participants->get($pid, [
		'contain' => ['Events'],
		]);
		if (empty($p)) {
			throw new NotFoundException("Impossibile trovare l'utente indicato");
		}

	  //Se l'utente ha già memorizzato una transazione ignoro la richiesta
		if (!empty($p->transaction_id)) {
			throw new ForbiddenException("La transazione di questo utente è già registrata");
		}

		$now = FrozenTime::now();
		$p->transaction_id = $transaction_id;
		$p->transaction_date = $now;
		$p->renewal_date = $now->month(12)->day(31);

	  //TODO Verificare che sia l'importo effettivamente pagato
		$p->amount = $p->event->cost;
		if ($this->Participants->save($p)) {
			$this->Flash->success('Pagamento ricevuto con successo');
		} else {
			$this->Flash->success('Impossibile registrare il pagamento, per favore contatta la segreteria@yepp.it');
		}
		$this->set('participant', $p);
	}
}
