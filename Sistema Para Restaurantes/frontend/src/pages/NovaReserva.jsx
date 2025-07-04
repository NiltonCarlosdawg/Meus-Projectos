import { useState, useCallback, useMemo } from "react";
import { BiX } from "react-icons/bi";
import { FaCalendar, FaClock, FaUser, FaChevronLeft, FaChevronRight } from "react-icons/fa";
import { MdTableRestaurant } from "react-icons/md";
import { TbLocationFilled } from "react-icons/tb";

// Mock data - replace with your actual imports
const passos = [
  { id: 1, nome: "Data & Hora" },
  { id: 2, nome: "Detalhes" }
];

const date = Array.from({ length: 10 }, (_, i) => i + 1);
const hora = ["18", "19", "20", "21", "22"];
const mesas = [
  { time: "18:00" },
  { time: "18:30" },
  { time: "19:00" },
  { time: "19:30" },
  { time: "20:00" },
  { time: "20:30" }
];

const Message = ({ message, type }) => (
  <div className={`fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
    type ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
  }`}>
    {message}
  </div>
);

const LoadingSkeleton = () => (
  <div className="space-y-4 mt-6">
    {[1, 2].map(i => (
      <div key={i} className="animate-pulse">
        <div className="h-24 bg-gradient-to-r from-gray-200 via-yellow-100 to-gray-200 rounded-lg"></div>
      </div>
    ))}
  </div>
);

const NewReserve = ({ setShow }) => {
  // Form state
  const [reservationData, setReservationData] = useState({
    date: "",
    time: "",
    people: "",
    name: "",
    phone: "",
    email: "",
    occasion: ""
  });

  // UI state
  const [currentStep, setCurrentStep] = useState(1);
  const [showCalendar, setShowCalendar] = useState(false);
  const [showTables, setShowTables] = useState(false);
  const [showMessage, setShowMessage] = useState(false);
  const [message, setMessage] = useState("");
  const [isSuccess, setIsSuccess] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [monthOffset, setMonthOffset] = useState(0);

  const location = "Vila alice ao lado do colégio bem dizer";

  // Date utilities
  const currentDate = new Date();
  const currentYear = currentDate.getFullYear();
  const currentMonth = currentDate.getMonth();
  const currentDay = currentDate.getDate();

  const months = [
    "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
    "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
  ];

  const displayMonth = useMemo(() => {
    const monthIndex = (currentMonth + monthOffset) % 12;
    return {
      name: months[monthIndex],
      index: monthIndex,
      year: currentYear + Math.floor((currentMonth + monthOffset) / 12)
    };
  }, [monthOffset, currentMonth, currentYear]);

  const getDaysInMonth = useCallback((month, year) => {
    return new Array(new Date(year, month + 1, 0).getDate())
      .fill(null)
      .map((_, i) => i + 1);
  }, []);

  const isDateDisabled = useCallback((day) => {
    if (monthOffset === 0) {
      return day < currentDay;
    }
    return monthOffset < 0;
  }, [monthOffset, currentDay]);

  // Event handlers
  const updateReservationData = useCallback((field, value) => {
    setReservationData(prev => ({ ...prev, [field]: value }));
  }, []);

  const showMessageWithTimeout = useCallback((msg, success = false) => {
    setMessage(msg);
    setIsSuccess(success);
    setShowMessage(true);
    setTimeout(() => setShowMessage(false), 3000);
  }, []);

  const handleDateSelect = useCallback((day) => {
    if (isDateDisabled(day)) return;
    
    const dateString = `${day} ${displayMonth.name}`;
    updateReservationData('date', dateString);
    setShowCalendar(false);
  }, [isDateDisabled, displayMonth.name, updateReservationData]);

  const handleFindTable = useCallback(() => {
    if (!reservationData.date || !reservationData.people) {
      showMessageWithTimeout("Preencha todas as caixas");
      return;
    }

    setIsLoading(true);
    setTimeout(() => {
      setIsLoading(false);
      setShowTables(true);
    }, 1500);
  }, [reservationData.date, reservationData.people, showMessageWithTimeout]);

  const handleTimeSelect = useCallback((time) => {
    updateReservationData('time', time);
    setCurrentStep(2);
  }, [updateReservationData]);

  const validateForm = useCallback(() => {
    const { name, phone, email, occasion } = reservationData;
    
    if (!name || !phone || !email || !occasion) {
      return "Preencha todas as caixas";
    }
    
    if (name.length < 5 || phone.length < 8 || email.length < 5 || occasion.length < 5) {
      return "Apenas acima de 5 caracteres";
    }
    
    return null;
  }, [reservationData]);

  const handleSubmit = useCallback(async () => {
    const validationError = validateForm();
    if (validationError) {
      showMessageWithTimeout(validationError);
      return;
    }

    try {
      setIsLoading(true);
      
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 2000));
      
      // Replace with actual API call
      // const response = await axios.post("http://localhost:3000/setreserva", reservationData);
      
      showMessageWithTimeout("Reserva feita com sucesso!", true);
      setTimeout(() => setShow(), 2000);
    } catch (error) {
      showMessageWithTimeout("Erro ao fazer reserva. Tente novamente.");
    } finally {
      setIsLoading(false);
    }
  }, [validateForm, showMessageWithTimeout, setShow]);

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      {showMessage && <Message message={message} type={isSuccess} />}
      
      <div className="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden overflow-y-auto">
        {/* Header */}
        <div className="flex items-center justify-between p-6 border-b border-gray-100">
          <h2 className="text-2xl font-bold text-gray-900">Reservas no Mon soir</h2>
          <button
            onClick={() => setShow()}
            className="p-2 hover:bg-gray-100 rounded-full transition-colors"
          >
            <BiX size={24} className="text-gray-600" />
          </button>
        </div>

        {/* Progress Steps */}
        <div className="px-6 py-4 border-b border-gray-100">
          <div className="flex gap-8">
            {passos.map((step) => (
              <div
                key={step.id}
                className={`pb-2 text-sm font-medium transition-colors ${
                  currentStep === step.id
                    ? 'text-green-600 border-b-2 border-green-600'
                    : 'text-gray-500'
                }`}
              >
                <span className="mr-2">{step.id}.</span>
                <span>{step.nome}</span>
              </div>
            ))}
          </div>
        </div>

        <div className="p-6 ">
          {currentStep === 1 ? (
            /* Step 1: Date & Time Selection */
            <div className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border border-gray-200 rounded-xl">
                {/* People Selector */}
                <select
                  className="p-3 bg-white border-0 focus:outline-none cursor-pointer text-gray-700"
                  onChange={(e) => updateReservationData('people', e.target.value)}
                  value={reservationData.people}
                >
                  <option value="">Pessoas</option>
                  {date.map((num) => (
                    <option key={num} value={`${num} ${num > 1 ? 'Pessoas' : 'Pessoa'}`}>
                      {num} {num > 1 ? 'Pessoas' : 'Pessoa'}
                    </option>
                  ))}
                </select>

                {/* Date Selector */}
                <div className="relative ">
                  <button
                    onClick={() => setShowCalendar(!showCalendar)}
                    className="w-full p-3 text-left bg-white border-0 focus:outline-none cursor-pointer text-gray-700 border-l border-gray-200"
                  >
                    {reservationData.date || "Data"}
                  </button>
                  
                  {showCalendar && (
                    <div className="absolute top-0 left-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-20">
                      <div className="p-4">
                        <div className="flex items-center justify-between mb-4">
                          <button
                            onClick={() => setMonthOffset(prev => prev - 1)}
                            className="p-2 hover:bg-gray-100 rounded-full transition-colors"
                          >
                            <FaChevronLeft size={16} />
                          </button>
                          <h3 className="text-lg font-semibold text-gray-900">
                            {displayMonth.name} {displayMonth.year}
                          </h3>
                          <button
                            onClick={() => setMonthOffset(prev => prev + 1)}
                            className="p-2 hover:bg-gray-100 rounded-full transition-colors"
                          >
                            <FaChevronRight size={16} />
                          </button>
                        </div>
                        
                        <div className="grid grid-cols-7 gap-1">
                          {getDaysInMonth(displayMonth.index, displayMonth.year).map((day) => (
                            <button
                              key={day}
                              onClick={() => handleDateSelect(day)}
                              disabled={isDateDisabled(day)}
                              className={`p-2 text-sm rounded-lg transition-colors ${
                                isDateDisabled(day)
                                  ? 'bg-red-100 text-red-400 cursor-not-allowed'
                                  : 'bg-gray-100 hover:bg-yellow-200 hover:text-black cursor-pointer'
                              }`}
                            >
                              {day}
                            </button>
                          ))}
                        </div>
                      </div>
                    </div>
                  )}
                </div>

                {/* Time Selector */}
                <select
                  className="p-3 bg-white border-0 focus:outline-none cursor-pointer text-gray-700 border-l border-gray-200"
                  value={reservationData.time}
                  onChange={(e) => updateReservationData('time', e.target.value)}
                >
                  <option value="">Horário</option>
                  {hora.map((time) => (
                    <option key={time} value={`${time}:00`}>
                      {time}:00
                    </option>
                  ))}
                </select>
              </div>

              <button
                onClick={handleFindTable}
                disabled={isLoading}
                className="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-6 rounded-xl transition-colors disabled:opacity-50"
              >
                {isLoading ? 'Procurando...' : 'Encontre a mesa'}
              </button>

              {/* Table Selection */}
              {isLoading ? (
                <LoadingSkeleton />
              ) : showTables ? (
                <div className="space-y-4">
                  <p className="text-sm text-gray-600">Escolha uma das mesas disponíveis:</p>
                  <div className="grid grid-cols-2 md:grid-cols-3 gap-3">
                    {mesas.map((mesa, index) => (
                      <button
                        key={index}
                        onClick={() => handleTimeSelect(mesa.time)}
                        className="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2 shadow-md hover:shadow-lg"
                      >
                        <MdTableRestaurant />
                        {mesa.time}
                      </button>
                    ))}
                  </div>
                </div>
              ) : null}
            </div>
          ) : (
            /* Step 2: Personal Details */
            <div className="space-y-6">
              <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="lg:col-span-2 space-y-4">
                  <input
                    type="text"
                    placeholder="Digite o seu nome"
                    value={reservationData.name}
                    onChange={(e) => updateReservationData('name', e.target.value)}
                    className="w-full p-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                  />
                  <input
                    type="tel"
                    placeholder="Número de telefone"
                    value={reservationData.phone}
                    onChange={(e) => updateReservationData('phone', e.target.value)}
                    className="w-full p-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                  />
                  <input
                    type="email"
                    placeholder="Email"
                    value={reservationData.email}
                    onChange={(e) => updateReservationData('email', e.target.value)}
                    className="w-full p-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                  />
                  <select
                    value={reservationData.occasion}
                    onChange={(e) => updateReservationData('occasion', e.target.value)}
                    className="w-full p-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                  >
                    <option value="">Selecione a Ocasião (opcional)</option>
                    <option value="Aniversário">Aniversário</option>
                    <option value="Negócios">Negócios</option>
                    <option value="Jantar Casual">Jantar Casual</option>
                  </select>
                </div>

                {/* Reservation Summary */}
                <div className="bg-gray-50 p-4 rounded-xl space-y-3">
                  <h3 className="font-bold text-lg text-gray-900">Mon soir</h3>
                  <div className="space-y-2 text-sm text-gray-600">
                    <div className="flex items-center gap-2">
                      <FaCalendar className="text-yellow-600" />
                      <span>{reservationData.date}</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <FaClock className="text-yellow-600" />
                      <span>{reservationData.time}</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <FaUser className="text-yellow-600" />
                      <span>{reservationData.people}</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <TbLocationFilled className="text-yellow-600" />
                      <span>{location}</span>
                    </div>
                  </div>
                </div>
              </div>

              <button
                onClick={handleSubmit}
                disabled={isLoading}
                className="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 px-6 rounded-xl transition-colors disabled:opacity-50"
              >
                {isLoading ? 'Confirmando...' : 'Confirmar Reserva'}
              </button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default NewReserve;