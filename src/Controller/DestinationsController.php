<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Destination;
use function Psy\debug;

/**
 * Destinations Controller
 *
 * @property \App\Model\Table\DestinationsTable $Destinations
 *
 * @method \App\Model\Entity\Destination[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DestinationsController extends AppController
{
    public $paginate = [
        'limit' => 9,
    ];

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow('view');
		$this->Auth->allow('index');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {        
        $destinations = $this->paginate($this->Destinations, [
            'contain' => ['Articles'],
            'conditions' => ['show_in_list' => TRUE],
            'order' => ['chiuso ASC', 'name'] ,
            'limit' => 100,          
        ] );
        $this->set(compact('destinations'));
    }

    public function adminIndex()
    {        
        $destinations = $this->paginate($this->Destinations, [
            'contain' => ['Articles'],
            'order' => ['name']            
        ] );
        $this->set(compact('destinations'));
    }

    /**
     * View method
     *
     * @param string|null $id Destination id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {        
        $query = $this->Destinations->find();        
        $a = $this->request->getQuery('archive');
        
        $articles_q = $this->Destinations->Articles->find()
                        ->order( ['modified' => 'DESC']); 

        if (empty($a)) { 
            $articles_q->where(['Articles.archived' => false]);
        }
        else {
            $articles_q->where(['Articles.archived' => true]);
        }        
        
        if (is_string($id))
        {
            try {
                $id = $this->Destinations->findBySlug($id)->firstOrFail()->id;                            
                
            } catch (RecordNotFoundException $ex) {                
                $this->log(sprintf('Record not found in database (id = %d)!', $id), LogLevel::WARNING);
            }
        }
         
        $articles_q->where(['destination_id'=>$id]); 
        $query->where(['id'=>$id]); 
        $destination = $query->first();

        $this->set('archived',$a);  
        $this->set('articles', $this->paginate($articles_q));
        $this->set('destination', $destination);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $destination = $this->Destinations->newEntity();
        if ($this->request->is('post')) {
            $destination = $this->Destinations->patchEntity($destination, $this->request->getData());
            if ($this->Destinations->save($destination)) {
                $this->Flash->success(__('The destination has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The destination could not be saved. Please, try again.'));
        }
        $this->set(compact('destination'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Destination id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $destination = $this->Destinations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $destination = $this->Destinations->patchEntity($destination, $this->request->getData());
            if ($this->Destinations->save($destination)) {
                $this->Flash->success(__('The destination has been saved.'));

                return $this->redirect(['action' => 'admin_index']);
            }
            $this->Flash->error(__('The destination could not be saved. Please, try again.'));
        }
        $this->set(compact('destination'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Destination id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $destination = $this->Destinations->get($id);
        if ($this->Destinations->delete($destination)) {
            $this->Flash->success(__('The destination has been deleted.'));
        } else {
            $this->Flash->error(__('The destination could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
